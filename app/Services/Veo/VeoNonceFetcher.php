<?php

namespace App\Services\Veo;

use Illuminate\Support\Facades\Http;

class VeoNonceFetcher
{
    private const VEO_VIDEO_GENERATOR_URL = 'https://veoaifree.com/veo-video-generator/';

    public function fetch(): ?string
    {
        $response = Http::timeout(20)
            ->connectTimeout(10)
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0',
                'Accept' => 'text/html,application/xhtml+xml',
            ])
            ->get(self::VEO_VIDEO_GENERATOR_URL)
            ->throw();

        preg_match('/nonce\\\\?":\\\\?"(.*?)\\\\?"/', $response->body(), $matches);

        return $matches[1] ?? null;
    }
}
