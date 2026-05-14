<?php

namespace App\Services\Veo;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Sleep;
use Illuminate\Support\Str;

class VeoVideoGenerator
{
    public function __construct(
        protected VeoNonceFetcher $nonceFetcher,
    ) {}

    public function generateVideoId(string $prompt): ?string
    {
        return $this->generateVideoIdResult($prompt)['generate_video_id'];
    }

    public function generateVideoUrl(string $prompt): ?string
    {
        return $this->generateVideoUrlResult($prompt)['video_url'];
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
     * @return array{nonce: ?string, generate_video_id: ?string, video_url: ?string}
     */
    public function generateVideoUrlResult(string $prompt): array
    {
        $nonce = $this->nonceFetcher->fetch();

        return $this->generateVideoUrlWithNonce($prompt, $nonce);
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
            $response = $this->newRequest()->send('POST', $this->videoGeneratorUrl(), [
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

    /**
     * @return array{nonce: ?string, generate_video_id: ?string, video_url: ?string}
     */
    public function generateVideoUrlWithNonce(
        string $prompt,
        ?string $nonce,
        int $maxAttempts = 10,
        int $pollDelayMilliseconds = 2000,
    ): array {
        if (blank($nonce)) {
            return [
                'nonce' => null,
                'generate_video_id' => null,
                'video_url' => null,
            ];
        }

        $generateVideo = $this->generateVideoIdWithNonce($prompt, $nonce);

        if (blank($generateVideo['generate_video_id']) || blank($generateVideo['nonce'])) {
            return [
                'nonce' => $generateVideo['nonce'],
                'generate_video_id' => $generateVideo['generate_video_id'],
                'video_url' => null,
            ];
        }

        $remainingAttempts = max(1, $maxAttempts);

        for ($attempt = 1; $attempt <= $remainingAttempts; $attempt++) {
            $videoResult = $this->fetchVideoUrlWithNonce(
                $generateVideo['generate_video_id'],
                $generateVideo['nonce'],
            );

            if (filled($videoResult['video_url'])) {
                return [
                    'nonce' => $videoResult['nonce'],
                    'generate_video_id' => $generateVideo['generate_video_id'],
                    'video_url' => $videoResult['video_url'],
                ];
            }

            if ($attempt < $remainingAttempts) {
                Sleep::for($pollDelayMilliseconds)->milliseconds();
            }
        }

        return [
            'nonce' => $generateVideo['nonce'],
            'generate_video_id' => $generateVideo['generate_video_id'],
            'video_url' => null,
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
            $response = $this->longRunningRequest()->send('POST', $this->videoGeneratorUrl(), [
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
            ->withHeaders($this->defaultHeaders());
    }

    private function longRunningRequest(): PendingRequest
    {
        return Http::withOptions([
            'timeout' => 0,
        ])->connectTimeout(10)
            ->withHeaders($this->defaultHeaders());
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
            'X-Requested-With' => (string) config('services.veo.headers.x_requested_with'),
            'Origin' => (string) config('services.veo.headers.origin'),
        ], fn (string $value): bool => filled($value));
    }

    private function videoGeneratorUrl(): string
    {
        return (string) config('services.veo.video_generator_url');
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
