<?php

namespace App\Services;

use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Бизнес-логика публикаций: создание и выборка с сортировками, фильтрами и порциями.
 */
final class PostService
{
    private const DEFAULT_LIMIT = 15;

    /** Допустимые варианты сортировки: по дате и по названию. */
    private const SORTS = [
        'date' => 'created_at',
        'title' => 'title',
    ];

    /**
     * Публикация поста от имени пользователя.
     *
     * @param  array{title: string, text: string}  $data
     */
    public function create(User $author, array $data): Post
    {
        return $author->posts()->create($data);
    }

    /**
     * Порционная выборка публикаций с фильтрацией и сортировкой.
     *
     * @param  array<string, mixed>  $filters  — нормализованные фильтры ListPostsRequest
     * @return array{posts: Collection<int, Post>, meta: array<string, int|string>}
     */
    public function list(?User $author, array $filters): array
    {
        $sort = $filters['sort'] ?? 'date';
        $order = $filters['order'] ?? 'desc';
        $limit = (int) ($filters['limit'] ?? self::DEFAULT_LIMIT);
        $offset = (int) ($filters['offset'] ?? 0);

        $query = Post::query()->with('author');

        if ($author !== null) {
            $query->where('user_id', $author->id);
        }

        if (filled($filters['date_from'] ?? null)) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (filled($filters['date_to'] ?? null)) {
            $query->where('created_at', '<=', Carbon::parse($filters['date_to'])->endOfDay());
        }

        $total = (clone $query)->count();

        $posts = $query
            ->orderBy(self::SORTS[$sort] ?? 'created_at', $order)
            // Вторичное поле для детерминированной пагинации
            ->orderBy('id', $order)
            ->offset($offset)
            ->limit($limit)
            ->get();

        return [
            'posts' => $posts,
            'meta' => [
                'total' => $total,
                'count' => $posts->count(),
                'limit' => $limit,
                'offset' => $offset,
                'sort' => $sort,
                'order' => $order,
            ],
        ];
    }
}
