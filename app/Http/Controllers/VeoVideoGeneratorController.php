<?php

namespace App\Http\Controllers;

use App\Http\Requests\VeoGenerateVideoRequest;
use App\Services\Veo\VeoVideoGenerator;
use Illuminate\Http\JsonResponse;

class VeoVideoGeneratorController extends Controller
{
    public function __invoke(
        VeoGenerateVideoRequest $request,
        VeoVideoGenerator $veoVideoGenerator,
    ): JsonResponse {
        $validated = $request->validated();

        return response()->json([
            'generate_video_id' => $veoVideoGenerator->generateVideoIdWithNonce(
                $validated['prompt'],
                $validated['nonce'],
            )['generate_video_id'],
        ]);
    }
}
