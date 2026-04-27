<?php

namespace App\Http\Controllers;

use App\Ai\Agents\Orion;
use App\Http\Requests\OrionChatRequest;
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
        $videoResult = filled($generateVideo['generate_video_id'])
            ? $veoVideoGenerator->fetchVideoUrlWithNonce(
                $generateVideo['generate_video_id'],
                $generateVideo['nonce'],
            )
            : ['nonce' => null, 'video_url' => null];

        return response()->json([
            'text' => $response->text,
            'conversation_id' => $response->conversationId,
            'veo_nonce' => $generateVideo['nonce'],
            'generate_video_id' => $generateVideo['generate_video_id'],
            'result_nonce' => $videoResult['nonce'],
            'video_url' => $videoResult['video_url'],
            'generate_nonce_found' => filled($generateVideo['nonce']),
            'generate_video_id_found' => filled($generateVideo['generate_video_id']),
            'result_nonce_found' => filled($videoResult['nonce']),
            'video_url_found' => filled($videoResult['video_url']),
        ]);
    }
}
