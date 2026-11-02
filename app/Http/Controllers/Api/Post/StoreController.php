<?php

namespace App\Http\Controllers\Api\Post;

use App\Http\Requests\CreatePostRequest;
use App\Http\Resources\PostResource;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;

final class StoreController
{
    public function __invoke(CreatePostRequest $request, PostService $posts): JsonResponse
    {
        $post = $posts->create($request->user(), $request->validated());

        return PostResource::make($post->loadMissing('author'))
            ->additional(['message' => 'Публикация сохранена.'])
            ->response()
            ->setStatusCode(201);
    }
}
