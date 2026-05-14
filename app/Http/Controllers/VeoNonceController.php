<?php

namespace App\Http\Controllers;

use App\Services\Veo\VeoNonceFetcher;
use Illuminate\Http\JsonResponse;

class VeoNonceController extends Controller
{
    public function __invoke(VeoNonceFetcher $veoNonceFetcher): JsonResponse
    {
        return response()->json([
            'nonce' => $veoNonceFetcher->fetch(),
        ]);
    }
}
