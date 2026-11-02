<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Api\Controllers\ApiController;
use App\Http\Helpers\ResponseHelper;
use App\Http\Requests\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

final class LoginController extends ApiController
{
    /**
     * @OA\Post(
     *     path="/login",
     *     tags={"Auth"},
     *     summary="Вход",
     *     description="Проверяет email и пароль, выдаёт пару токенов: access_token (ability: access, 60 минут) и refresh_token (ability: refresh, 30 дней).",
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="application/json",
     *
     *             @OA\Schema(
     *
     *                 @OA\Property(property="email", type="string", example="user@blog.local"),
     *                 @OA\Property(property="password", type="string", format="password", example="password")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Вход выполнен, пара токенов выдана",
     *
     *         @OA\JsonContent(ref="#/components/schemas/LoginResponse")
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Ошибка валидации полей",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Неверный email или пароль",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function __invoke(LoginRequest $request, AuthService $auth): JsonResponse
    {
        $result = $auth->login($request->toDto());

        return response()->json(ResponseHelper::authPayload($result));
    }
}
