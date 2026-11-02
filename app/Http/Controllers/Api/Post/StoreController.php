<?php

namespace App\Http\Controllers\Api\Post;

use App\Http\Api\Controllers\ApiController;
use App\Http\Requests\CreatePostRequest;
use App\Http\Resources\PostResource;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;

final class StoreController extends ApiController
{
    /**
     * @OA\Post(
     *     path="/posts",
     *     tags={"Posts"},
     *     summary="Публикация поста",
     *     description="Создаёт публикацию от имени текущего пользователя (владельца access-токена).",
     *     security={{"accessToken": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="application/json",
     *
     *             @OA\Schema(
     *
     *                 @OA\Property(property="title", type="string", example="Заголовок публикации"),
     *                 @OA\Property(property="text", type="string", example="Текст публикации.")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Публикация создана",
     *
     *         @OA\JsonContent(ref="#/components/schemas/StorePostResponse")
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
     *         description="Не авторизован",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function __invoke(CreatePostRequest $request, PostService $posts): JsonResponse
    {
        $post = $posts->create($request->user(), $request->toDto());

        return response()->json(
            ['post' => PostResource::make($post->loadMissing('author'))],
            201,
        );
    }
}
