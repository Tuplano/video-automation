<?php

namespace App\Services\Veo;

use Illuminate\Support\Facades\Http;

class VeoVideoGenerator
{
    private const VEO_GENERATE_VIDEO_URL = 'https://veoaifree.com/wp-admin/admin-ajax.php';

    private const DEFAULT_HEADERS = [
        'User-Agent' => 'Mozilla/5.0',
        'Accept' => 'application/json, text/plain, */*',
    ];

    public function __construct(
        protected VeoNonceFetcher $nonceFetcher,
    ) {}

    public function generateVideoId(string $prompt): ?string
    {
        $nonce = $this->nonceFetcher->fetch();

        if (blank($nonce)) {
            return null;
        }

        $response = $this->newRequest()->post(self::VEO_GENERATE_VIDEO_URL, [
            'action' => 'veo_video_generator',
            'nonce' => $nonce,
            'prompt' => $prompt,
            'totalVariations' => '1',
            'aspectRatio' => 'VIDEO_ASPECT_RATIO_PORTRAIT',
            'actionType' => 'full-video-generate',
        ])
            ->throw();

        $data = $response->json();

        return data_get($data, 'data.generate_video_id')
    }

    public function fetchVideoUrl(string $sceneData): ?string
    {
        $nonce = $this->nonceFetcher->fetch();

        if (blank($nonce)) {
            return null;
        }

        $response = $this->newRequest()->post(self::VEO_GENERATE_VIDEO_URL, [
            'action' => 'veo_video_generator',
            'nonce' => $nonce,
            'sceneData' => $sceneData,
            'actionType' => 'final-video-results',
        ])
            ->throw();

        $data = $response->json();

        return data_get($data, 'data.video_url')
    }

    private function newRequest()
    {
        return Http::asForm()
            ->timeout(30)
            ->connectTimeout(10)
            ->withHeaders(self::DEFAULT_HEADERS);
    }
}
