<?php

namespace App\Http\Controllers;

use App\Http\Requests\VeoFinalVideoRequest;
use App\Services\Veo\VeoVideoGenerator;
use Illuminate\Http\JsonResponse;

class VeoFinalVideoController extends Controller
{
    public function __invoke(
        VeoFinalVideoRequest $request,
        VeoVideoGenerator $veoVideoGenerator,
    ): JsonResponse {
        $validated = $request->validated();

        return response()->json([
            'video_url' => $veoVideoGenerator->fetchVideoUrlWithNonce(
                $validated['generate_video_id'],
                $validated['nonce'],
            )['video_url'],
        ]);
    }
}
