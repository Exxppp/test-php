<?php

namespace App\Http\Dto;

/**
 * Нормализованные параметры выборки публикаций (лента / мои публикации)
 * со значениями по умолчанию.
 */
final readonly class ListPostData
{
    public const DEFAULT_LIMIT = 15;

    public function __construct(
        public string $sort = 'date',
        public string $order = 'desc',
        public int $limit = self::DEFAULT_LIMIT,
        public int $offset = 0,
        public ?string $dateFrom = null,
        public ?string $dateTo = null,
    ) {}
}
