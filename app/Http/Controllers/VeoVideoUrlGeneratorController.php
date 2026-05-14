<?php

namespace App\Http\Controllers;

use App\Http\Requests\VeoGenerateVideoRequest;
use App\Services\Veo\VeoVideoGenerator;
use Illuminate\Http\JsonResponse;

class VeoVideoUrlGeneratorController extends Controller
{
    public function __invoke(
        VeoGenerateVideoRequest $request,
        VeoVideoGenerator $veoVideoGenerator,
    ): JsonResponse {
        $validated = $request->validated();

        return response()->json([
            'video_url' => $veoVideoGenerator->generateVideoUrlWithNonce(
                $validated['prompt'],
                $validated['nonce'],
            )['video_url'],
        ]);
    }
}
