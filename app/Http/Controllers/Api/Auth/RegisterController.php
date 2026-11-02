<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

final class RegisterController
{
    public function __invoke(RegisterRequest $request, AuthService $auth): JsonResponse
    {
        $result = $auth->register($request->validated());

        return response()->json([
            'user' => new UserResource($result->user),
            'accessToken' => $result->accessToken,
            'refreshToken' => $result->refreshToken,
        ], 201);
    }
}
