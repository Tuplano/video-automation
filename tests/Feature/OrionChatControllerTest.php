<?php

use App\Ai\Agents\Orion;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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

test('guests are redirected when posting to orion chat', function () {
    $response = $this->postJson(route('orion.chat.store'), [
        'message' => 'What is the weather in Manila?',
    ]);

    $response->assertUnauthorized();
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
