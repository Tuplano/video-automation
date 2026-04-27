<?php

use App\Services\Veo\VeoNonceFetcher;
use App\Services\Veo\VeoVideoGenerator;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Mockery\MockInterface;

test('veo video generator can create a generate video id', function () {
    Http::preventStrayRequests();

    $this->mock(VeoNonceFetcher::class, function (MockInterface $mock): void {
        $mock->shouldReceive('fetch')->once()->andReturn('abc123xyz');
    });

    Http::fake([
        'veoaifree.com/*' => Http::response([
            'success' => true,
            'data' => [
                'generate_video_id' => 'video_job_123',
            ],
        ], 200),
    ]);

    $generateVideoId = app(VeoVideoGenerator::class)
        ->generateVideoId('A calm rainy city street at night with reflections on wet pavement.');

    expect($generateVideoId)->toBe('video_job_123');

    Http::assertSent(function (Request $request): bool {
        return $request->url() === 'https://veoaifree.com/wp-admin/admin-ajax.php'
            && $request->method() === 'POST'
            && $request->hasHeader('User-Agent', 'Mozilla/5.0')
            && $request->hasHeader('Accept', 'application/json, text/plain, */*')
            && $request['action'] === 'veo_video_generator'
            && $request['nonce'] === 'abc123xyz'
            && $request['prompt'] === 'A calm rainy city street at night with reflections on wet pavement.'
            && $request['totalVariations'] === '1'
            && $request['aspectRatio'] === 'VIDEO_ASPECT_RATIO_PORTRAIT'
            && $request['actionType'] === 'full-video-generate';
    });
});

test('veo video generator returns null when no nonce is available', function () {
    Http::preventStrayRequests();

    $this->mock(VeoNonceFetcher::class, function (MockInterface $mock): void {
        $mock->shouldReceive('fetch')->once()->andReturnNull();
    });

    $generateVideoId = app(VeoVideoGenerator::class)
        ->generateVideoId('A bright tropical beach sunrise with gentle waves.');

    expect($generateVideoId)->toBeNull();

    Http::assertNothingSent();
});

test('veo video generator returns null when the response has no generate video id', function () {
    Http::preventStrayRequests();

    $this->mock(VeoNonceFetcher::class, function (MockInterface $mock): void {
        $mock->shouldReceive('fetch')->once()->andReturn('abc123xyz');
    });

    Http::fake([
        'veoaifree.com/*' => Http::response([
            'success' => true,
            'data' => [],
        ], 200),
    ]);

    $generateVideoId = app(VeoVideoGenerator::class)
        ->generateVideoId('A storm rolling over mountain peaks at dusk.');

    expect($generateVideoId)->toBeNull();
});

test('veo video generator can fetch a final video url', function () {
    Http::preventStrayRequests();

    $this->mock(VeoNonceFetcher::class, function (MockInterface $mock): void {
        $mock->shouldReceive('fetch')->once()->andReturn('abc123xyz');
    });

    Http::fake([
        'veoaifree.com/*' => Http::response([
            'success' => true,
            'data' => [
                'video_url' => 'https://cdn.example.com/generated/final-video.mp4',
            ],
        ], 200),
    ]);

    $videoUrl = app(VeoVideoGenerator::class)
        ->fetchVideoUrl('video_job_123');

    expect($videoUrl)->toBe('https://cdn.example.com/generated/final-video.mp4');

    Http::assertSent(function (Request $request): bool {
        return $request->url() === 'https://veoaifree.com/wp-admin/admin-ajax.php'
            && $request->method() === 'POST'
            && $request['action'] === 'veo_video_generator'
            && $request['nonce'] === 'abc123xyz'
            && $request['sceneData'] === 'video_job_123'
            && $request['actionType'] === 'final-video-results';
    });
});

test('veo video generator returns null when the final video url is missing', function () {
    Http::preventStrayRequests();

    $this->mock(VeoNonceFetcher::class, function (MockInterface $mock): void {
        $mock->shouldReceive('fetch')->once()->andReturn('abc123xyz');
    });

    Http::fake([
        'veoaifree.com/*' => Http::response([
            'success' => true,
            'data' => [],
        ], 200),
    ]);

    $videoUrl = app(VeoVideoGenerator::class)
        ->fetchVideoUrl('video_job_123');

    expect($videoUrl)->toBeNull();
});
