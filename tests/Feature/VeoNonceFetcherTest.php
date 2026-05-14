<?php

use App\Services\Veo\VeoNonceFetcher;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    setVeoNonceTestConfig();
});

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
        $headers = veoTestHeaders();
        $videoPageUrl = (string) config('services.veo.video_page_url');

        return $request->url() === $videoPageUrl
            && $request->method() === 'GET'
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
            && $request->hasHeader('User-Agent', $headers['user_agent']);
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

test('veo nonce fetcher uses configured veo url and headers', function () {
    Http::preventStrayRequests();

    Config::set('services.veo.video_page_url', 'https://veoaifree.com/custom-page/');
    Config::set('services.veo.headers.cookie', 'session=test-cookie');

    Http::fake([
        'veoaifree.com/*' => Http::response(
            '<script>window.__DATA__ = {"nonce":"config123"};</script>',
            200
        ),
    ]);

    $nonce = app(VeoNonceFetcher::class)->fetch();

    expect($nonce)->toBe('config123');

    Http::assertSent(function (Request $request): bool {
        return $request->url() === (string) config('services.veo.video_page_url')
            && $request->hasHeader('Cookie', 'session=test-cookie');
    });
});
