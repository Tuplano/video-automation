<?php

namespace App\Http\Controllers;

use App\Ai\Agents\Orion;
use App\Http\Requests\OrionChatRequest;
use Illuminate\Http\JsonResponse;

class OrionChatController extends Controller
{
    /**
     * Send a message to Orion and return the response.
     */
    public function store(OrionChatRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $agent = new Orion;

        $response = filled($validated['conversation_id'] ?? null)
            ? $agent->continue($validated['conversation_id'], as: $request->user())
                ->prompt($validated['message'])
            : $agent->forUser($request->user())
                ->prompt($validated['message']);

        return response()->json([
            'text' => $response->text,
            'conversation_id' => $response->conversationId,
        ]);
    }
}
