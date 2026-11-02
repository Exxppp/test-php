<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Api\Controllers\ApiController;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

final class RefreshController extends ApiController
{
    /**
     * @OA\Post(
     *     path="/refresh",
     *     tags={"Auth"},
     *     summary="Ротация пары токенов",
     *     description="Принимает refresh_token (ability: refresh), удаляет его и выдаёт новую пару access_token + refresh_token. Пользователь в ответе не возвращается.",
     *     security={{"accessToken": {}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Новая пара токенов",
     *
     *         @OA\JsonContent(ref="#/components/schemas/RefreshResponse")
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Токен не передан, недействителен или истёк",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function __invoke(AuthService $auth): JsonResponse
    {
        $result = $auth->refresh();

        return response()->json([
            'access_token' => $result->accessToken,
            'refresh_token' => $result->refreshToken,
        ]);
    }
}
