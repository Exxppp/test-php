<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Api\Controllers\ApiController;
use App\Http\Helpers\ResponseHelper;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

final class RegisterController extends ApiController
{
    /**
     * @OA\Post(
     *     path="/register",
     *     tags={"Auth"},
     *     summary="Регистрация пользователя",
     *     description="Создаёт пользователя и выдаёт пару токенов: access_token (ability: access, 60 минут) и refresh_token (ability: refresh, 30 дней).",
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="application/json",
     *
     *             @OA\Schema(
     *
     *                 @OA\Property(property="name", type="string", example="Иван Иванов"),
     *                 @OA\Property(property="email", type="string", example="ivan@example.com"),
     *                 @OA\Property(property="password", type="string", format="password", example="password")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Пользователь зарегистрирован, пара токенов выдана",
     *
     *         @OA\JsonContent(ref="#/components/schemas/RegisterResponse")
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
     *         response=409,
     *         description="Пользователь с таким email уже зарегистрирован",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function __invoke(RegisterRequest $request, AuthService $auth): JsonResponse
    {
        $result = $auth->register($request->toDto());

        return response()->json(ResponseHelper::authPayload($result), 201);
    }
}
