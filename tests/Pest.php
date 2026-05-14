<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind different classes or traits.
|
*/

pest()->extend(TestCase::class)
 // ->use(RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

/**
 * @return array<string, string>
 */
function veoTestHeaders(bool $includeAjaxHeaders = false): array
{
    $headers = [
        'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8',
        'accept_encoding' => 'gzip, deflate, br, zstd',
        'accept_language' => 'en-US,en;q=0.8',
        'cache_control' => 'max-age=0',
        'cookie' => 'popupLockout=active; ytPopup=1; socialPopup=1; adsensePopUp; videoCounter=3; adsense=46',
        'priority' => 'u=0, i',
        'referer' => 'https://veoaifree.com/',
        'sec_ch_ua' => '"Not:A-Brand";v="99", "Brave";v="145", "Chromium";v="145"',
        'sec_ch_ua_mobile' => '?0',
        'sec_ch_ua_platform' => '"Linux"',
        'sec_fetch_dest' => 'document',
        'sec_fetch_mode' => 'navigate',
        'sec_fetch_site' => 'same-origin',
        'sec_fetch_user' => '?1',
        'sec_gpc' => '1',
        'upgrade_insecure_requests' => '1',
        'user_agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36',
    ];

    if ($includeAjaxHeaders) {
        $headers['x_requested_with'] = 'XMLHttpRequest';
        $headers['origin'] = 'https://veoaifree.com';
    }

    return $headers;
}

function setVeoNonceTestConfig(?string $videoPageUrl = null): void
{
    Config::set('services.veo.video_page_url', $videoPageUrl ?? 'https://veoaifree.com/veo-video-generator/');
    Config::set('services.veo.headers', veoTestHeaders());
}

function setVeoVideoGeneratorTestConfig(?string $videoGeneratorUrl = null): void
{
    Config::set('services.veo.video_generator_url', $videoGeneratorUrl ?? 'https://veoaifree.com/wp-admin/admin-ajax.php');
    Config::set('services.veo.headers', veoTestHeaders(includeAjaxHeaders: true));
}
