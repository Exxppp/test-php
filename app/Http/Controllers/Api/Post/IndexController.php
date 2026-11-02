<?php

namespace App\Http\Controllers\Api\Post;

use App\Http\Requests\ListPostsRequest;
use App\Http\Resources\PostResource;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;

/**
 * Общая лента публикаций (порционно).
 */
final class IndexController
{
    public function __invoke(ListPostsRequest $request, PostService $posts): JsonResponse
    {
        $result = $posts->list(author: null, filters: $request->filters());

        return response()->json([
            'posts' => PostResource::collection($result['posts']),
            'pagination' => $result['meta'],
        ]);
    }
}
