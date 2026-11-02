<?php

namespace App\Http\Dto;

final readonly class ListPostData
{
    public function __construct(
        public string $sort,
        public string $order,
        public int $limit,
        public int $offset,
        public ?string $dateFrom = null,
        public ?string $dateTo = null,
    ) {}
}
