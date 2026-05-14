<?php

namespace App\Services\Veo;

use Illuminate\Support\Facades\Http;

class VeoNonceFetcher
{
    public function fetch(): ?string
    {
        $response = Http::timeout(20)
            ->connectTimeout(10)
            ->withHeaders($this->defaultHeaders())
            ->get($this->videoPageUrl())
            ->throw();

        preg_match('/nonce\\\\?":\\\\?"(.*?)\\\\?"/', $response->body(), $matches);

        return $matches[1] ?? null;
    }

    /**
     * @return array<string, string>
     */
    private function defaultHeaders(): array
    {
        return array_filter([
            'Accept' => (string) config('services.veo.headers.accept'),
            'Accept-Encoding' => (string) config('services.veo.headers.accept_encoding'),
            'Accept-Language' => (string) config('services.veo.headers.accept_language'),
            'Cache-Control' => (string) config('services.veo.headers.cache_control'),
            'Cookie' => (string) config('services.veo.headers.cookie'),
            'Priority' => (string) config('services.veo.headers.priority'),
            'Referer' => (string) config('services.veo.headers.referer'),
            'Sec-CH-UA' => (string) config('services.veo.headers.sec_ch_ua'),
            'Sec-CH-UA-Mobile' => (string) config('services.veo.headers.sec_ch_ua_mobile'),
            'Sec-CH-UA-Platform' => (string) config('services.veo.headers.sec_ch_ua_platform'),
            'Sec-Fetch-Dest' => (string) config('services.veo.headers.sec_fetch_dest'),
            'Sec-Fetch-Mode' => (string) config('services.veo.headers.sec_fetch_mode'),
            'Sec-Fetch-Site' => (string) config('services.veo.headers.sec_fetch_site'),
            'Sec-Fetch-User' => (string) config('services.veo.headers.sec_fetch_user'),
            'Sec-GPC' => (string) config('services.veo.headers.sec_gpc'),
            'Upgrade-Insecure-Requests' => (string) config('services.veo.headers.upgrade_insecure_requests'),
            'User-Agent' => (string) config('services.veo.headers.user_agent'),
        ], fn (string $value): bool => filled($value));
    }

    private function videoPageUrl(): string
    {
        return (string) config('services.veo.video_page_url');
    }
}
