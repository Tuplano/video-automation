<?php

namespace App\Services\Veo;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class VeoVideoGenerator
{
    private const VEO_GENERATE_VIDEO_URL = 'https://veoaifree.com/wp-admin/admin-ajax.php';

    private const DEFAULT_HEADERS = [
        'User-Agent' => 'Mozilla/5.0',
        'Accept' => 'application/json, text/plain, */*',
        'X-Requested-With' => 'XMLHttpRequest',
        'Origin' => 'https://veoaifree.com',
        'Referer' => 'https://veoaifree.com/veo-video-generator/',
    ];

    public function __construct(
        protected VeoNonceFetcher $nonceFetcher,
    ) {}

    public function generateVideoId(string $prompt): ?string
    {
        return $this->generateVideoIdResult($prompt)['generate_video_id'];
    }

    /**
     * @return array{nonce: ?string, generate_video_id: ?string}
     */
    public function generateVideoIdResult(string $prompt): array
    {
        $nonce = $this->nonceFetcher->fetch();

        return $this->generateVideoIdWithNonce($prompt, $nonce);
    }

    /**
     * @return array{nonce: ?string, generate_video_id: ?string}
     */
    public function generateVideoIdWithNonce(string $prompt, ?string $nonce): array
    {
        if (blank($nonce)) {
            return [
                'nonce' => null,
                'generate_video_id' => null,
            ];
        }

        try {
            $response = $this->newRequest()->send('POST', self::VEO_GENERATE_VIDEO_URL, [
                'multipart' => $this->multipartFields([
                    'action' => 'veo_video_generator',
                    'nonce' => $nonce,
                    'prompt' => $prompt,
                    'totalVariations' => '1',
                    'aspectRatio' => 'VIDEO_ASPECT_RATIO_PORTRAIT',
                    'actionType' => 'full-video-generate',
                ]),
            ])
                ->throw();
        } catch (ConnectionException|RequestException) {
            return [
                'nonce' => $nonce,
                'generate_video_id' => null,
            ];
        }

        return [
            'nonce' => $nonce,
            'generate_video_id' => $this->extractGenerateVideoId($response->body()),
        ];
    }

    public function fetchVideoUrl(string $sceneData): ?string
    {
        return $this->fetchVideoUrlResult($sceneData)['video_url'];
    }

    /**
     * @return array{nonce: ?string, video_url: ?string}
     */
    public function fetchVideoUrlResult(string $sceneData): array
    {
        $nonce = $this->nonceFetcher->fetch();

        return $this->fetchVideoUrlWithNonce($sceneData, $nonce);
    }

    /**
     * @return array{nonce: ?string, video_url: ?string}
     */
    public function fetchVideoUrlWithNonce(string $sceneData, ?string $nonce): array
    {
        if (blank($nonce)) {
            return [
                'nonce' => null,
                'video_url' => null,
            ];
        }

        try {
            $response = $this->longRunningRequest()->send('POST', self::VEO_GENERATE_VIDEO_URL, [
                'multipart' => $this->multipartFields([
                    'action' => 'veo_video_generator',
                    'nonce' => $nonce,
                    'sceneData' => $sceneData,
                    'actionType' => 'final-video-results',
                ]),
            ])
                ->throw();
        } catch (ConnectionException|RequestException) {
            return [
                'nonce' => $nonce,
                'video_url' => null,
            ];
        }

        return [
            'nonce' => $nonce,
            'video_url' => $this->extractVideoUrl($response->body()),
        ];
    }

    private function newRequest(): PendingRequest
    {
        return Http::timeout(30)
            ->connectTimeout(10)
            ->withHeaders(self::DEFAULT_HEADERS);
    }

    private function longRunningRequest(): PendingRequest
    {
        return Http::withOptions([
            'timeout' => 0,
        ])->connectTimeout(10)
            ->withHeaders(self::DEFAULT_HEADERS);
    }

    /**
     * @param  array<string, string>  $fields
     * @return array<int, array{name: string, contents: string}>
     */
    private function multipartFields(array $fields): array
    {
        return collect($fields)
            ->map(fn (string $value, string $name): array => [
                'name' => $name,
                'contents' => $value,
            ])
            ->values()
            ->all();
    }

    private function extractGenerateVideoId(string $body): ?string
    {
        $trimmedBody = trim($body);

        if (preg_match('/^\d+$/', $trimmedBody) === 1) {
            return $trimmedBody;
        }

        if (Str::isJson($trimmedBody)) {
            /** @var mixed $data */
            $data = json_decode($trimmedBody, true);

            return data_get($data, 'data.generate_video_id')
                ?? data_get($data, 'generate_video_id')
                ?? data_get($data, 'data.video_id')
                ?? data_get($data, 'video_id')
                ?? $this->extractNumericIdFromText($trimmedBody);
        }

        return $this->extractNumericIdFromText($trimmedBody);
    }

    private function extractVideoUrl(string $body): ?string
    {
        $trimmedBody = trim($body);

        if (preg_match('/https?:\/\/[^\s"\']+\.mp4/i', $trimmedBody, $matches) === 1) {
            return $this->normalizeVideoUrl($matches[0]);
        }

        if (Str::isJson($trimmedBody)) {
            /** @var mixed $data */
            $data = json_decode($trimmedBody, true);

            $videoUrl = data_get($data, 'data.video_url')
                ?? data_get($data, 'video_url')
                ?? data_get($data, 'data.url')
                ?? data_get($data, 'url')
                ?? data_get($data, 'data.videoUrl')
                ?? data_get($data, 'videoUrl');

            return is_string($videoUrl)
                ? $this->normalizeVideoUrl($videoUrl)
                : null;
        }

        return null;
    }

    private function normalizeVideoUrl(string $videoUrl): string
    {
        return str_replace('/videos/uploads/', '/video/uploads/', $videoUrl);
    }

    private function extractNumericIdFromText(string $text): ?string
    {
        if (preg_match('/\b\d{5,}\b/', $text, $matches) === 1) {
            return $matches[0];
        }

        return null;
    }
}
