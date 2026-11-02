<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

final class LoginController
{
    public function __invoke(LoginRequest $request, AuthService $auth): JsonResponse
    {
        $result = $auth->login($request->validated());

        return response()->json([
            'accessToken' => $result->accessToken,
            'refreshToken' => $result->refreshToken,
            'tokenType' => 'Bearer',
            'user' => new UserResource($result->user),
        ]);
    }
}
