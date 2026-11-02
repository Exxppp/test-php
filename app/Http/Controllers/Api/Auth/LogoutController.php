<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Api\Controllers\ApiController;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

final class LogoutController extends ApiController
{
    /**
     * @OA\Post(
     *     path="/logout",
     *     tags={"Auth"},
     *     summary="Выход",
     *     description="Отзывает все токены текущего пользователя (доступ по access_token).",
     *     security={{"accessToken": {}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Токены отозваны",
     *
     *         @OA\JsonContent(ref="#/components/schemas/LogoutResponse")
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Не авторизован",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function __invoke(AuthService $auth): JsonResponse
    {
        $auth->logout();

        // Обёртку {meta, data} накладывает middleware TransformApiResponse
        return response()->json([]);
    }
}
