<?php

use App\Ai\Agents\Orion;
use App\Models\User;
use App\Services\Veo\VeoVideoGenerator;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Mockery\MockInterface;

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

    $this->mock(VeoVideoGenerator::class, function (MockInterface $mock): void {
        $mock->shouldReceive('generateVideoIdResult')->andReturn([
            'nonce' => null,
            'generate_video_id' => null,
        ]);

        $mock->shouldReceive('fetchVideoUrlWithNonce')->andReturn([
            'nonce' => null,
            'video_url' => null,
        ]);
    });
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
        ->assertJsonPath('conversation_id', null)
        ->assertJsonPath('veo_nonce', null)
        ->assertJsonPath('generate_video_id', null)
        ->assertJsonPath('result_nonce', null)
        ->assertJsonPath('video_status', 'unavailable')
        ->assertJsonPath('generate_nonce_found', false)
        ->assertJsonPath('generate_video_id_found', false)
        ->assertJsonPath('result_nonce_found', false)
        ->assertJsonPath('video_url_found', false)
        ->assertJsonPath('video_url', null);

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
        ->assertJsonPath('conversation_id', null)
        ->assertJsonPath('veo_nonce', null)
        ->assertJsonPath('generate_video_id', null)
        ->assertJsonPath('result_nonce', null)
        ->assertJsonPath('video_status', 'unavailable')
        ->assertJsonPath('generate_nonce_found', false)
        ->assertJsonPath('generate_video_id_found', false)
        ->assertJsonPath('result_nonce_found', false)
        ->assertJsonPath('video_url_found', false)
        ->assertJsonPath('video_url', null);
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
        ->assertJsonPath('text', 'Manila is warm and cloudy today.')
        ->assertJsonPath('veo_nonce', null)
        ->assertJsonPath('generate_video_id', null)
        ->assertJsonPath('result_nonce', null)
        ->assertJsonPath('video_status', 'unavailable')
        ->assertJsonPath('generate_nonce_found', false)
        ->assertJsonPath('generate_video_id_found', false)
        ->assertJsonPath('result_nonce_found', false)
        ->assertJsonPath('video_url_found', false)
        ->assertJsonPath('video_url', null);

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
        ->assertJsonPath('conversation_id', $conversationId)
        ->assertJsonPath('veo_nonce', null)
        ->assertJsonPath('generate_video_id', null)
        ->assertJsonPath('result_nonce', null)
        ->assertJsonPath('video_status', 'unavailable')
        ->assertJsonPath('generate_nonce_found', false)
        ->assertJsonPath('generate_video_id_found', false)
        ->assertJsonPath('result_nonce_found', false)
        ->assertJsonPath('video_url_found', false)
        ->assertJsonPath('video_url', null);

    expect(DB::table('agent_conversation_messages')
        ->where('conversation_id', $conversationId)
        ->count())->toBe(4);

    Orion::assertPrompted('What is the weather in Manila today?');
    Orion::assertPrompted('What about tomorrow?');
});

test('orion chat can return a generated video id and mark the video as processing', function () {
    Orion::fake([
        'A cinematic vertical video of a rainy Manila street at night with neon reflections and slow camera movement.',
    ])->preventStrayPrompts();

    $this->mock(VeoVideoGenerator::class, function (MockInterface $mock): void {
        $mock->shouldReceive('generateVideoIdResult')
            ->once()
            ->with('A cinematic vertical video of a rainy Manila street at night with neon reflections and slow camera movement.')
            ->andReturn([
                'nonce' => 'nonce_abc123',
                'generate_video_id' => 'video_job_123',
            ]);

        $mock->shouldReceive('fetchVideoUrlWithNonce')->never();
    });

    $response = $this->postJson(route('orion.chat.store'), [
        'message' => 'Generate a rainy Manila night drive video prompt.',
    ]);

    $response->assertOk()
        ->assertJsonPath('veo_nonce', 'nonce_abc123')
        ->assertJsonPath('generate_video_id', 'video_job_123')
        ->assertJsonPath('result_nonce', null)
        ->assertJsonPath('video_status', 'processing')
        ->assertJsonPath('generate_nonce_found', true)
        ->assertJsonPath('generate_video_id_found', true)
        ->assertJsonPath('result_nonce_found', false)
        ->assertJsonPath('video_url_found', false)
        ->assertJsonPath('video_url', null);
});

test('orion video status can return a ready video url', function () {
    $this->mock(VeoVideoGenerator::class, function (MockInterface $mock): void {
        $mock->shouldReceive('fetchVideoUrlWithNonce')
            ->once()
            ->with('video_job_123', 'nonce_abc123')
            ->andReturn([
                'nonce' => 'nonce_abc123',
                'video_url' => 'https://cdn.example.com/generated/final-video.mp4',
            ]);
    });

    $response = $this->getJson(route('orion.chat.status', [
        'generate_video_id' => 'video_job_123',
        'nonce' => 'nonce_abc123',
    ]));

    $response->assertOk()
        ->assertJsonPath('generate_video_id', 'video_job_123')
        ->assertJsonPath('result_nonce', 'nonce_abc123')
        ->assertJsonPath('video_status', 'ready')
        ->assertJsonPath('result_nonce_found', true)
        ->assertJsonPath('video_url_found', true)
        ->assertJsonPath('video_url', 'https://cdn.example.com/generated/final-video.mp4');
});

test('orion video status remains processing when no video url is ready yet', function () {
    $this->mock(VeoVideoGenerator::class, function (MockInterface $mock): void {
        $mock->shouldReceive('fetchVideoUrlWithNonce')
            ->once()
            ->with('video_job_123', 'nonce_abc123')
            ->andReturn([
                'nonce' => 'nonce_abc123',
                'video_url' => null,
            ]);
    });

    $response = $this->getJson(route('orion.chat.status', [
        'generate_video_id' => 'video_job_123',
        'nonce' => 'nonce_abc123',
    ]));

    $response->assertOk()
        ->assertJsonPath('generate_video_id', 'video_job_123')
        ->assertJsonPath('result_nonce', 'nonce_abc123')
        ->assertJsonPath('video_status', 'processing')
        ->assertJsonPath('result_nonce_found', true)
        ->assertJsonPath('video_url_found', false)
        ->assertJsonPath('video_url', null);
});
