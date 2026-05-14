<?php

use App\Services\Veo\VeoVideoGenerator;
use Mockery\MockInterface;

test('veo video generator api returns only the generate video id', function () {
    $this->mock(VeoVideoGenerator::class, function (MockInterface $mock): void {
        $mock->shouldReceive('generateVideoIdWithNonce')
            ->once()
            ->with(
                'A cinematic vertical video of a rainy Manila street at night.',
                'nonce_abc123',
            )
            ->andReturn([
                'nonce' => 'nonce_abc123',
                'generate_video_id' => 'video_job_123',
            ]);
    });

    $response = $this->postJson(route('veo.generate'), [
        'nonce' => 'nonce_abc123',
        'prompt' => 'A cinematic vertical video of a rainy Manila street at night.',
    ]);

    $response->assertOk()
        ->assertExactJson([
            'generate_video_id' => 'video_job_123',
        ]);
});

test('veo video generator api returns null when generation is unavailable', function () {
    $this->mock(VeoVideoGenerator::class, function (MockInterface $mock): void {
        $mock->shouldReceive('generateVideoIdWithNonce')
            ->once()
            ->with('A soft sunrise over a quiet beach.', 'nonce_abc123')
            ->andReturn([
                'nonce' => 'nonce_abc123',
                'generate_video_id' => null,
            ]);
    });

    $response = $this->postJson(route('veo.generate'), [
        'nonce' => 'nonce_abc123',
        'prompt' => 'A soft sunrise over a quiet beach.',
    ]);

    $response->assertOk()
        ->assertExactJson([
            'generate_video_id' => null,
        ]);
});

test('veo video generator api validates nonce and prompt', function () {
    $response = $this->postJson(route('veo.generate'), []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['nonce', 'prompt']);
});

test('veo video generator api is excluded from csrf protection', function () {
    $this->mock(VeoVideoGenerator::class, function (MockInterface $mock): void {
        $mock->shouldReceive('generateVideoIdWithNonce')
            ->once()
            ->with('A cinematic skyline at dusk.', 'nonce_abc123')
            ->andReturn([
                'nonce' => 'nonce_abc123',
                'generate_video_id' => 'video_job_123',
            ]);
    });

    $response = $this->withMiddleware()->postJson(route('veo.generate'), [
        'nonce' => 'nonce_abc123',
        'prompt' => 'A cinematic skyline at dusk.',
    ]);

    $response->assertOk()
        ->assertExactJson([
            'generate_video_id' => 'video_job_123',
        ]);
});
