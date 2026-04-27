<?php

use App\Services\Veo\VeoNonceFetcher;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

test('veo nonce fetcher can extract a nonce from html', function () {
    Http::preventStrayRequests();

    Http::fake([
        'veoaifree.com/*' => Http::response(
            '<script>window.__DATA__ = {"nonce":"abc123xyz"};</script>',
            200
        ),
    ]);

    $nonce = app(VeoNonceFetcher::class)->fetch();

    expect($nonce)->toBe('abc123xyz');

    Http::assertSent(function (Request $request): bool {
        return $request->url() === 'https://veoaifree.com/veo-video-generator/'
            && $request->method() === 'GET'
            && $request->hasHeader('User-Agent', 'Mozilla/5.0')
            && $request->hasHeader('Accept', 'text/html,application/xhtml+xml');
    });
});

test('veo nonce fetcher returns null when the nonce is missing', function () {
    Http::preventStrayRequests();

    Http::fake([
        'veoaifree.com/*' => Http::response('<html><body>No nonce here.</body></html>', 200),
    ]);

    $nonce = app(VeoNonceFetcher::class)->fetch();

    expect($nonce)->toBeNull();
});
