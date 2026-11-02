<?php

namespace App\Http\Controllers\Api\Post;

use App\Http\Api\Controllers\ApiController;
use App\Http\Requests\ListPostsRequest;
use App\Http\Resources\PostResource;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;

/**
 * Публикации текущего пользователя (порционно).
 */
final class MyController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/posts/my",
     *     tags={"Posts"},
     *     summary="Мои публикации",
     *     description="Порционная выдача публикаций текущего пользователя — владельца access-токена. Параметры выборки те же, что и в общей ленте.",
     *     security={{"accessToken": {}}},
     *
     *     @OA\Parameter(name="sort", in="query", @OA\Schema(type="string", enum={"date", "title"}, default="date"), description="Сортировка: date — по дате создания, title — по названию"),
     *     @OA\Parameter(name="order", in="query", @OA\Schema(type="string", enum={"asc", "desc"}, default="desc"), description="Направление сортировки"),
     *     @OA\Parameter(name="limit", in="query", @OA\Schema(type="integer", minimum=1, maximum=100, default=15), description="Размер порции"),
     *     @OA\Parameter(name="offset", in="query", @OA\Schema(type="integer", minimum=0, default=0), description="Смещение"),
     *     @OA\Parameter(name="date_from", in="query", @OA\Schema(type="string", format="date"), description="Публикации, созданные не ранее (YYYY-MM-DD)"),
     *     @OA\Parameter(name="date_to", in="query", @OA\Schema(type="string", format="date"), description="Публикации, созданные не позднее (YYYY-MM-DD, включительно)"),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Порция публикаций и параметры выборки",
     *
     *         @OA\JsonContent(ref="#/components/schemas/FeedResponse")
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Некорректные параметры выборки",
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
    public function __invoke(ListPostsRequest $request, PostService $posts): JsonResponse
    {
        $feed = $posts->list($request->user(), $request->toDto());

        return response()->json([
            'posts' => PostResource::collection($feed),
        ]);
    }
}
