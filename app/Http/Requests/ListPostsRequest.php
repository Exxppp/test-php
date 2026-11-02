<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Входящие параметры выборки публикаций (лента / мои публикации).
 */
class ListPostsRequest extends FormRequest
{
    private const DEFAULT_LIMIT = 15;

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
     *
     * @return array<string, mixed>
     */
    public function filters(): array
    {
        return [
            'sort' => $this->input('sort', 'date'),
            'order' => $this->input('order', 'desc'),
            'limit' => $this->input('limit', self::DEFAULT_LIMIT),
            'offset' => $this->input('offset', 0),
            'date_from' => $this->input('date_from'),
            'date_to' => $this->input('date_to'),
        ];
    }
}
