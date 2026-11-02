<?php

namespace App\Http\Requests;

use App\Http\Dto\ListPostData;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Входящие параметры выборки публикаций (лента / мои публикации).
 */
class ListPostsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'sort' => ['nullable', 'string', Rule::in(['date', 'title'])],
            'order' => ['nullable', 'string', Rule::in(['asc', 'desc'])],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'offset' => ['nullable', 'integer', 'min:0'],
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
            sort: (string) $this->input('sort', 'date'),
            order: (string) $this->input('order', 'desc'),
            limit: (int) $this->input('limit', ListPostData::DEFAULT_LIMIT),
            offset: (int) $this->input('offset', 0),
            dateFrom: $this->input('date_from'),
            dateTo: $this->input('date_to'),
        );
    }
}
