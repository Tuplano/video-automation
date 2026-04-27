<?php

use App\Services\Veo\VeoNonceFetcher;
use App\Services\Veo\VeoVideoGenerator;
use Illuminate\Http\Client\ConnectionException;
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
        $body = $request->body();

        return $request->url() === 'https://veoaifree.com/wp-admin/admin-ajax.php'
            && $request->method() === 'POST'
            && $request->hasHeader('User-Agent', 'Mozilla/5.0')
            && $request->hasHeader('Accept', 'application/json, text/plain, */*')
            && $request->hasHeader('X-Requested-With', 'XMLHttpRequest')
            && $request->hasHeader('Origin', 'https://veoaifree.com')
            && $request->hasHeader('Referer', 'https://veoaifree.com/veo-video-generator/')
            && str_contains($request->header('Content-Type')[0] ?? '', 'multipart/form-data')
            && str_contains($body, 'name="action"')
            && str_contains($body, 'veo_video_generator')
            && str_contains($body, 'name="nonce"')
            && str_contains($body, 'abc123xyz')
            && str_contains($body, 'name="prompt"')
            && str_contains($body, 'A calm rainy city street at night with reflections on wet pavement.')
            && str_contains($body, 'name="totalVariations"')
            && str_contains($body, 'name="aspectRatio"')
            && str_contains($body, 'VIDEO_ASPECT_RATIO_PORTRAIT')
            && str_contains($body, 'name="actionType"')
            && str_contains($body, 'full-video-generate');
    });
});

test('veo video generator can extract a plain numeric video id response', function () {
    Http::preventStrayRequests();

    $this->mock(VeoNonceFetcher::class, function (MockInterface $mock): void {
        $mock->shouldReceive('fetch')->once()->andReturn('abc123xyz');
    });

    Http::fake([
        'veoaifree.com/*' => Http::response('482653', 200),
    ]);

    $generateVideoId = app(VeoVideoGenerator::class)
        ->generateVideoId('A fluffy ginger tabby dancing in a neon-lit living room.');

    expect($generateVideoId)->toBe('482653');
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

test('veo video generator returns null when generate video id request times out', function () {
    Http::preventStrayRequests();

    $this->mock(VeoNonceFetcher::class, function (MockInterface $mock): void {
        $mock->shouldReceive('fetch')->once()->andReturn('abc123xyz');
    });

    Http::fake([
        'veoaifree.com/*' => fn () => throw new ConnectionException('Timed out'),
    ]);

    $generateVideoId = app(VeoVideoGenerator::class)
        ->generateVideoId('A quiet rainy street in Makati at dusk.');

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
        $body = $request->body();

        return $request->url() === 'https://veoaifree.com/wp-admin/admin-ajax.php'
            && $request->method() === 'POST'
            && $request->hasHeader('X-Requested-With', 'XMLHttpRequest')
            && $request->hasHeader('Origin', 'https://veoaifree.com')
            && $request->hasHeader('Referer', 'https://veoaifree.com/veo-video-generator/')
            && str_contains($request->header('Content-Type')[0] ?? '', 'multipart/form-data')
            && str_contains($body, 'name="action"')
            && str_contains($body, 'veo_video_generator')
            && str_contains($body, 'name="nonce"')
            && str_contains($body, 'abc123xyz')
            && str_contains($body, 'name="sceneData"')
            && str_contains($body, 'video_job_123')
            && str_contains($body, 'name="actionType"')
            && str_contains($body, 'final-video-results');
    });
});

test('veo video generator can extract a plain mp4 url response', function () {
    Http::preventStrayRequests();

    $this->mock(VeoNonceFetcher::class, function (MockInterface $mock): void {
        $mock->shouldReceive('fetch')->once()->andReturn('abc123xyz');
    });

    Http::fake([
        'veoaifree.com/*' => Http::response(
            'https://veoaifree.com/videos/uploads/video_482653_1777257471.mp4',
            200
        ),
    ]);

    $videoUrl = app(VeoVideoGenerator::class)
        ->fetchVideoUrl('482653');

    expect($videoUrl)->toBe('https://veoaifree.com/videos/uploads/video_482653_1777257471.mp4');
});

test('veo video generator can reuse the same nonce for final video lookup', function () {
    Http::preventStrayRequests();

    Http::fake([
        'veoaifree.com/*' => Http::response([
            'success' => true,
            'data' => [
                'video_url' => 'https://cdn.example.com/generated/reused-nonce-video.mp4',
            ],
        ], 200),
    ]);

    $videoResult = app(VeoVideoGenerator::class)
        ->fetchVideoUrlWithNonce('video_job_123', 'shared_nonce_789');

    expect($videoResult)->toBe([
        'nonce' => 'shared_nonce_789',
        'video_url' => 'https://cdn.example.com/generated/reused-nonce-video.mp4',
    ]);

    Http::assertSent(function (Request $request): bool {
        $body = $request->body();

        return $request->url() === 'https://veoaifree.com/wp-admin/admin-ajax.php'
            && $request->method() === 'POST'
            && $request->hasHeader('X-Requested-With', 'XMLHttpRequest')
            && $request->hasHeader('Origin', 'https://veoaifree.com')
            && $request->hasHeader('Referer', 'https://veoaifree.com/veo-video-generator/')
            && str_contains($request->header('Content-Type')[0] ?? '', 'multipart/form-data')
            && str_contains($body, 'shared_nonce_789')
            && str_contains($body, 'name="sceneData"')
            && str_contains($body, 'video_job_123')
            && str_contains($body, 'final-video-results');
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

test('veo video generator returns null when final video lookup times out', function () {
    Http::preventStrayRequests();

    $this->mock(VeoNonceFetcher::class, function (MockInterface $mock): void {
        $mock->shouldReceive('fetch')->once()->andReturn('abc123xyz');
    });

    Http::fake([
        'veoaifree.com/*' => fn () => throw new ConnectionException('Timed out'),
    ]);

    $videoUrl = app(VeoVideoGenerator::class)
        ->fetchVideoUrl('video_job_123');

    expect($videoUrl)->toBeNull();
});
