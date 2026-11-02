<?php

namespace App\Services;

use App\Http\Dto\CreatePostData;
use App\Http\Dto\ListPostData;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Бизнес-логика публикаций: создание и выборка с сортировками, фильтрами и порциями.
 */
final class PostService
{
    /** Допустимые варианты сортировки: по дате и по названию. */
    private const SORTS = [
        'date' => 'created_at',
        'title' => 'title',
    ];

    /**
     * Публикация поста от имени пользователя.
     */
    public function create(User $author, CreatePostData $data): Post
    {
        return $author->posts()->create($data->toArray());
    }

    /**
     * Порционная выборка публикаций с фильтрацией и сортировкой.
     *
     * @return array{posts: Collection<int, Post>, meta: array<string, int|string>}
     */
    public function list(?User $author, ListPostData $filters): array
    {
        $query = Post::query()->with('author');

        if ($author !== null) {
            $query->where('user_id', $author->id);
        }

        if ($filters->dateFrom !== null) {
            $query->where('created_at', '>=', $filters->dateFrom);
        }

        if ($filters->dateTo !== null) {
            $query->where('created_at', '<=', Carbon::parse($filters->dateTo)->endOfDay());
        }

        $total = (clone $query)->count();

        $posts = $query
            ->orderBy(self::SORTS[$filters->sort] ?? 'created_at', $filters->order)
            // Вторичное поле для детерминированной пагинации
            ->orderBy('id', $filters->order)
            ->offset($filters->offset)
            ->limit($filters->limit)
            ->get();

        return [
            'posts' => $posts,
            'meta' => [
                'total' => $total,
                'count' => $posts->count(),
                'limit' => $filters->limit,
                'offset' => $filters->offset,
                'sort' => $filters->sort,
                'order' => $filters->order,
            ],
        ];
    }
}
