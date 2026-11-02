<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

final class RefreshController
{
    public function __invoke(AuthService $auth): JsonResponse
    {
        $result = $auth->refresh();

        return response()->json([
            'user' => new UserResource($result->user),
            'accessToken' => $result->accessToken,
            'refreshToken' => $result->refreshToken,
        ]);
    }
}
