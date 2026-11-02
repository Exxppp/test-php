<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Helpers\ResponseHelper;
use App\Http\Requests\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

final class LoginController
{
    public function __invoke(LoginRequest $request, AuthService $auth): JsonResponse
    {
        $result = $auth->login($request->toDto());

        return response()->json(ResponseHelper::authPayload($result));
    }
}
