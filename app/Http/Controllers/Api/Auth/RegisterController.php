<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Helpers\ResponseHelper;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

final class RegisterController
{
    public function __invoke(RegisterRequest $request, AuthService $auth): JsonResponse
    {
        $result = $auth->register($request->toDto());

        return response()->json(ResponseHelper::authPayload($result), 201);
    }
}
