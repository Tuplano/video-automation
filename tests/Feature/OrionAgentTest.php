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

test('orion remembers a user conversation', function () {
    $user = User::factory()->create();

    Orion::fake([
        'Let us map out your day.',
        'You wanted help planning your day.',
    ]);

    $firstResponse = (new Orion)->forUser($user)->prompt('Help me plan my day.');

    expect($firstResponse->conversationId)->not->toBeNull();

    $conversationId = $firstResponse->conversationId;

    expect(DB::table('agent_conversations')
        ->where('id', $conversationId)
        ->where('user_id', $user->id)
        ->exists())->toBeTrue();

    expect(DB::table('agent_conversation_messages')
        ->where('conversation_id', $conversationId)
        ->count())->toBe(2);

    $continuedAgent = (new Orion)->continue($conversationId, as: $user);
    $messages = collect($continuedAgent->messages());

    expect($messages)->toHaveCount(2);
    expect($messages->pluck('content')->all())->toBe([
        'Help me plan my day.',
        'Let us map out your day.',
    ]);

    $secondResponse = $continuedAgent->prompt('What was I asking about?');

    expect($secondResponse->conversationId)->toBe($conversationId);

    expect(DB::table('agent_conversation_messages')
        ->where('conversation_id', $conversationId)
        ->count())->toBe(4);
});
