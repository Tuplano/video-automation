<?php

use App\Ai\Agents\Orion;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    if (! Schema::hasTable('users')) {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->text('two_factor_secret')->nullable();
            $table->text('two_factor_recovery_codes')->nullable();
            $table->timestamp('two_factor_confirmed_at')->nullable();
            $table->timestamps();
        });
    }

    if (! Schema::hasTable('agent_conversations')) {
        Schema::create('agent_conversations', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->foreignId('user_id')->nullable();
            $table->string('title');
            $table->timestamps();
        });
    }

    if (! Schema::hasTable('agent_conversation_messages')) {
        Schema::create('agent_conversation_messages', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('conversation_id', 36)->index();
            $table->foreignId('user_id')->nullable();
            $table->string('agent');
            $table->string('role', 25);
            $table->text('content');
            $table->text('attachments');
            $table->text('tool_calls');
            $table->text('tool_results');
            $table->text('usage');
            $table->text('meta');
            $table->timestamps();
        });
    }
});

test('guests can post to orion chat without starting a remembered conversation', function () {
    Orion::fake([
        'Manila is hot and sunny today.',
    ])->preventStrayPrompts();

    $response = $this->postJson(route('orion.chat.store'), [
        'message' => 'What is the weather in Manila?',
    ]);

    $response->assertOk()
        ->assertJsonPath('text', 'Manila is hot and sunny today.')
        ->assertJsonPath('conversation_id', null);

    expect(DB::table('agent_conversations')->count())->toBe(0);
    expect(DB::table('agent_conversation_messages')->count())->toBe(0);

    Orion::assertPrompted('What is the weather in Manila?');
});

test('orion chat is excluded from csrf protection', function () {
    Orion::fake([
        'Cebu is breezy today.',
    ])->preventStrayPrompts();

    $response = $this->withMiddleware()->postJson(route('orion.chat.store'), [
        'message' => 'What is the weather in Cebu?',
    ]);

    $response->assertOk()
        ->assertJsonPath('text', 'Cebu is breezy today.')
        ->assertJsonPath('conversation_id', null);
});

test('orion chat is rate limited for guests', function () {
    Orion::fake(array_fill(0, 20, 'Davao is humid today.'))->preventStrayPrompts();

    Cache::flush();
    RateLimiter::clear('laravel_cache:'.sha1('POST|orion/chat|127.0.0.1'));

    foreach (range(1, 20) as $attempt) {
        $response = $this->withMiddleware()
            ->withServerVariables(['REMOTE_ADDR' => '127.0.0.1'])
            ->postJson(route('orion.chat.store'), [
                'message' => "Weather request {$attempt}",
            ]);

        $response->assertOk();
    }

    $response = $this->withMiddleware()
        ->withServerVariables(['REMOTE_ADDR' => '127.0.0.1'])
        ->postJson(route('orion.chat.store'), [
            'message' => 'Weather request 21',
        ]);

    $response->assertStatus(429);
});

test('authenticated users can start and continue an orion conversation', function () {
    Orion::fake([
        'Manila is warm and cloudy today.',
        'Manila Weather',
        'Tomorrow should be a little wetter, so bring an umbrella.',
    ])->preventStrayPrompts();

    $user = User::factory()->create();

    $firstResponse = $this->actingAs($user)->postJson(route('orion.chat.store'), [
        'message' => 'What is the weather in Manila today?',
    ]);

    $firstResponse->assertOk()
        ->assertJsonPath('text', 'Manila is warm and cloudy today.');

    $conversationId = $firstResponse->json('conversation_id');

    expect($conversationId)->not->toBeNull();

    expect(DB::table('agent_conversations')
        ->where('id', $conversationId)
        ->where('user_id', $user->id)
        ->exists())->toBeTrue();

    $secondResponse = $this->actingAs($user)->postJson(route('orion.chat.store'), [
        'message' => 'What about tomorrow?',
        'conversation_id' => $conversationId,
    ]);

    $secondResponse->assertOk()
        ->assertJsonPath('text', 'Tomorrow should be a little wetter, so bring an umbrella.')
        ->assertJsonPath('conversation_id', $conversationId);

    expect(DB::table('agent_conversation_messages')
        ->where('conversation_id', $conversationId)
        ->count())->toBe(4);

    Orion::assertPrompted('What is the weather in Manila today?');
    Orion::assertPrompted('What about tomorrow?');
});
