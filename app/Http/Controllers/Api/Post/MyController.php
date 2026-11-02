<?php

namespace App\Http\Controllers\Api\Post;

use App\Http\Requests\ListPostsRequest;
use App\Http\Resources\PostResource;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;

/**
 * Публикации текущего пользователя (порционно).
 */
final class MyController
{
    public function __invoke(ListPostsRequest $request, PostService $posts): JsonResponse
    {
        $result = $posts->list($request->user(), $request->filters());

        return response()->json([
            'posts' => PostResource::collection($result['posts']),
            'pagination' => $result['meta'],
        ]);
    }
}
