<?php

namespace App\Http\Controllers\Api\Auth;

use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

final class LogoutController
{
    public function __invoke(AuthService $auth): JsonResponse
    {
        $auth->logout();

        // Обёртку {meta, data} накладывает middleware TransformApiResponse
        return response()->json([]);
    }
}
