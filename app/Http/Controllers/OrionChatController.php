<?php

namespace App\Http\Controllers;

use App\Ai\Agents\Orion;
use App\Http\Requests\OrionChatRequest;
use App\Http\Requests\OrionVideoStatusRequest;
use App\Services\Veo\VeoVideoGenerator;
use Illuminate\Http\JsonResponse;

class OrionChatController extends Controller
{
    /**
     * Send a message to Orion and return the response.
     */
    public function store(OrionChatRequest $request, VeoVideoGenerator $veoVideoGenerator): JsonResponse
    {
        $validated = $request->validated();
        $agent = new Orion;
        $user = $request->user();

        $response = match (true) {
            filled($validated['conversation_id'] ?? null) && $user !== null => $agent->continue($validated['conversation_id'], as: $user)
                ->prompt($validated['message']),
            $user !== null => $agent->forUser($user)
                ->prompt($validated['message']),
            default => $agent->prompt($validated['message']),
        };

        $generateVideo = $veoVideoGenerator->generateVideoIdResult($response->text);

        return response()->json([
            'text' => $response->text,
            'conversation_id' => $response->conversationId,
            'veo_nonce' => $generateVideo['nonce'],
            'generate_video_id' => $generateVideo['generate_video_id'],
            'result_nonce' => null,
            'video_url' => null,
            'video_status' => filled($generateVideo['generate_video_id']) ? 'processing' : 'unavailable',
            'generate_nonce_found' => filled($generateVideo['nonce']),
            'generate_video_id_found' => filled($generateVideo['generate_video_id']),
            'result_nonce_found' => false,
            'video_url_found' => false,
        ]);
    }

    public function status(
        OrionVideoStatusRequest $request,
        VeoVideoGenerator $veoVideoGenerator,
    ): JsonResponse {
        $validated = $request->validated();

        $videoResult = $veoVideoGenerator->fetchVideoUrlWithNonce(
            $validated['generate_video_id'],
            $validated['nonce'],
        );

        return response()->json([
            'generate_video_id' => $validated['generate_video_id'],
            'result_nonce' => $videoResult['nonce'],
            'video_url' => $videoResult['video_url'],
            'video_status' => filled($videoResult['video_url']) ? 'ready' : 'processing',
            'result_nonce_found' => filled($videoResult['nonce']),
            'video_url_found' => filled($videoResult['video_url']),
        ]);
    }
}
