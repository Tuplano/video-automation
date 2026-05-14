<?php

use App\Services\Veo\VeoVideoGenerator;
use Mockery\MockInterface;

test('veo final video api returns only the video url', function () {
    $this->mock(VeoVideoGenerator::class, function (MockInterface $mock): void {
        $mock->shouldReceive('fetchVideoUrlWithNonce')
            ->once()
            ->with('video_job_123', 'nonce_abc123')
            ->andReturn([
                'nonce' => 'nonce_abc123',
                'video_url' => 'https://cdn.example.com/generated/final-video.mp4',
            ]);
    });

    $response = $this->postJson(route('veo.video-url'), [
        'nonce' => 'nonce_abc123',
        'generate_video_id' => 'video_job_123',
    ]);

    $response->assertOk()
        ->assertExactJson([
            'video_url' => 'https://cdn.example.com/generated/final-video.mp4',
        ]);
});

test('veo final video api returns null when final video is unavailable', function () {
    $this->mock(VeoVideoGenerator::class, function (MockInterface $mock): void {
        $mock->shouldReceive('fetchVideoUrlWithNonce')
            ->once()
            ->with('video_job_123', 'nonce_abc123')
            ->andReturn([
                'nonce' => 'nonce_abc123',
                'video_url' => null,
            ]);
    });

    $response = $this->postJson(route('veo.video-url'), [
        'nonce' => 'nonce_abc123',
        'generate_video_id' => 'video_job_123',
    ]);

    $response->assertOk()
        ->assertExactJson([
            'video_url' => null,
        ]);
});

test('veo final video api validates nonce and generate video id', function () {
    $response = $this->postJson(route('veo.video-url'), []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['nonce', 'generate_video_id']);
});

test('veo final video api is excluded from csrf protection', function () {
    $this->mock(VeoVideoGenerator::class, function (MockInterface $mock): void {
        $mock->shouldReceive('fetchVideoUrlWithNonce')
            ->once()
            ->with('video_job_123', 'nonce_abc123')
            ->andReturn([
                'nonce' => 'nonce_abc123',
                'video_url' => 'https://cdn.example.com/generated/final-video.mp4',
            ]);
    });

    $response = $this->withMiddleware()->postJson(route('veo.video-url'), [
        'nonce' => 'nonce_abc123',
        'generate_video_id' => 'video_job_123',
    ]);

    $response->assertOk()
        ->assertExactJson([
            'video_url' => 'https://cdn.example.com/generated/final-video.mp4',
        ]);
});
