<?php

use App\Services\Veo\VeoVideoGenerator;
use Mockery\MockInterface;

test('veo video url generator api returns only the video url', function () {
    $this->mock(VeoVideoGenerator::class, function (MockInterface $mock): void {
        $mock->shouldReceive('generateVideoUrlWithNonce')
            ->once()
            ->with('A neon-lit rainy city street.', 'nonce_abc123')
            ->andReturn([
                'nonce' => 'nonce_abc123',
                'generate_video_id' => 'video_job_123',
                'video_url' => 'https://cdn.example.com/generated/final-video.mp4',
            ]);
    });

    $response = $this->postJson(route('veo.generate-video-url'), [
        'nonce' => 'nonce_abc123',
        'prompt' => 'A neon-lit rainy city street.',
    ]);

    $response->assertOk()
        ->assertExactJson([
            'video_url' => 'https://cdn.example.com/generated/final-video.mp4',
        ]);
});

test('veo video url generator api returns null when no final video url is ready', function () {
    $this->mock(VeoVideoGenerator::class, function (MockInterface $mock): void {
        $mock->shouldReceive('generateVideoUrlWithNonce')
            ->once()
            ->with('A quiet beach sunrise.', 'nonce_abc123')
            ->andReturn([
                'nonce' => 'nonce_abc123',
                'generate_video_id' => 'video_job_123',
                'video_url' => null,
            ]);
    });

    $response = $this->postJson(route('veo.generate-video-url'), [
        'nonce' => 'nonce_abc123',
        'prompt' => 'A quiet beach sunrise.',
    ]);

    $response->assertOk()
        ->assertExactJson([
            'video_url' => null,
        ]);
});

test('veo video url generator api validates nonce and prompt', function () {
    $response = $this->postJson(route('veo.generate-video-url'), []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['nonce', 'prompt']);
});

test('veo video url generator api is excluded from csrf protection', function () {
    $this->mock(VeoVideoGenerator::class, function (MockInterface $mock): void {
        $mock->shouldReceive('generateVideoUrlWithNonce')
            ->once()
            ->with('A glowing skyline at dusk.', 'nonce_abc123')
            ->andReturn([
                'nonce' => 'nonce_abc123',
                'generate_video_id' => 'video_job_123',
                'video_url' => 'https://cdn.example.com/generated/skyline.mp4',
            ]);
    });

    $response = $this->withMiddleware()->postJson(route('veo.generate-video-url'), [
        'nonce' => 'nonce_abc123',
        'prompt' => 'A glowing skyline at dusk.',
    ]);

    $response->assertOk()
        ->assertExactJson([
            'video_url' => 'https://cdn.example.com/generated/skyline.mp4',
        ]);
});
