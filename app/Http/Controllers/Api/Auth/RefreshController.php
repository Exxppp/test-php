<?php

namespace App\Http\Controllers\Api\Auth;

use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

final class RefreshController
{
    public function __invoke(AuthService $auth): JsonResponse
    {
        $result = $auth->refresh();

        return response()->json([
            'access_token' => $result->accessToken,
            'refresh_token' => $result->refreshToken,
        ]);
    }
}
