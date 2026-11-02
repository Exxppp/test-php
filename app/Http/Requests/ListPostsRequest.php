<?php

namespace App\Http\Requests;

use App\Http\Dto\ListPostData;

/**
 * Входящие параметры выборки публикаций (лента / мои публикации).
 */
class ListPostsRequest extends ListRequest
{
    protected function allowedSorts(): array
    {
        return ['date', 'title'];
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            ...$this->sortRules(),
            ...$this->paginationRules(),
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
        ];
    }

    /**
     * Нормализованные параметры выборки со значениями по умолчанию.
     */
    public function toDto(): ListPostData
    {
        return new ListPostData(
            ...$this->paging(),
            sort: (string) $this->input('sort', 'date'),
            order: (string) $this->input('order', 'desc'),
            dateFrom: $this->input('date_from'),
            dateTo: $this->input('date_to'),
        );
    }
}
