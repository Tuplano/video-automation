<?php

use App\Services\Veo\VeoNonceFetcher;
use App\Services\Veo\VeoVideoGenerator;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Mockery\MockInterface;

beforeEach(function () {
    setVeoVideoGeneratorTestConfig();
});

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
        $headers = veoTestHeaders(includeAjaxHeaders: true);
        $videoGeneratorUrl = (string) config('services.veo.video_generator_url');

        return $request->url() === $videoGeneratorUrl
            && $request->method() === 'POST'
            && $request->hasHeader('Accept', $headers['accept'])
            && $request->hasHeader('Accept-Encoding', $headers['accept_encoding'])
            && $request->hasHeader('Accept-Language', $headers['accept_language'])
            && $request->hasHeader('Cache-Control', $headers['cache_control'])
            && $request->hasHeader('Cookie', $headers['cookie'])
            && $request->hasHeader('Priority', $headers['priority'])
            && $request->hasHeader('Referer', $headers['referer'])
            && $request->hasHeader('Sec-CH-UA', $headers['sec_ch_ua'])
            && $request->hasHeader('Sec-CH-UA-Mobile', $headers['sec_ch_ua_mobile'])
            && $request->hasHeader('Sec-CH-UA-Platform', $headers['sec_ch_ua_platform'])
            && $request->hasHeader('Sec-Fetch-Dest', $headers['sec_fetch_dest'])
            && $request->hasHeader('Sec-Fetch-Mode', $headers['sec_fetch_mode'])
            && $request->hasHeader('Sec-Fetch-Site', $headers['sec_fetch_site'])
            && $request->hasHeader('Sec-Fetch-User', $headers['sec_fetch_user'])
            && $request->hasHeader('Sec-GPC', $headers['sec_gpc'])
            && $request->hasHeader('Upgrade-Insecure-Requests', $headers['upgrade_insecure_requests'])
            && $request->hasHeader('User-Agent', $headers['user_agent'])
            && $request->hasHeader('X-Requested-With', $headers['x_requested_with'])
            && $request->hasHeader('Origin', $headers['origin'])
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
        $videoGeneratorUrl = (string) config('services.veo.video_generator_url');

        return $request->url() === $videoGeneratorUrl
            && $request->method() === 'POST'
            && $request->hasHeader('Accept', 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8')
            && $request->hasHeader('X-Requested-With', 'XMLHttpRequest')
            && $request->hasHeader('Origin', 'https://veoaifree.com')
            && $request->hasHeader('Referer', 'https://veoaifree.com/')
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

    expect($videoUrl)->toBe('https://veoaifree.com/video/uploads/video_482653_1777257471.mp4');
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
        $videoGeneratorUrl = (string) config('services.veo.video_generator_url');

        return $request->url() === $videoGeneratorUrl
            && $request->method() === 'POST'
            && $request->hasHeader('Accept', 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8')
            && $request->hasHeader('X-Requested-With', 'XMLHttpRequest')
            && $request->hasHeader('Origin', 'https://veoaifree.com')
            && $request->hasHeader('Referer', 'https://veoaifree.com/')
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
