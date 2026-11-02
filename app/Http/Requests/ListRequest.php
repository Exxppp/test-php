<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

/**
 * Базовый листовой запрос: пагинация и сортировка описаны один раз.
 */
abstract class ListRequest extends ApiRequest
{
    public const DEFAULT_LIMIT = 15;

    /**
     * Общие правила пагинации всех листовых запросов.
     *
     * @return array<string, array<int, mixed>>
     */
    protected function paginationRules(): array
    {
        return [
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'offset' => ['nullable', 'integer', 'min:0'],
        ];
    }

    /**
     * Общие правила сортировки: whitelist полей задаёт наследник.
     *
     * @return array<string, array<int, mixed>>
     */
    protected function sortRules(): array
    {
        return [
            'sort' => ['nullable', 'string', Rule::in($this->allowedSorts())],
            'order' => ['nullable', 'string', Rule::in(['asc', 'desc'])],
        ];
    }

    /**
     * Пагинация со значениями по умолчанию — для сборки DTO.
     *
     * @return array{limit: int, offset: int}
     */
    protected function paging(): array
    {
        return [
            'limit' => (int) $this->input('limit', self::DEFAULT_LIMIT),
            'offset' => (int) $this->input('offset', 0),
        ];
    }

    /**
     * Допустимые для сортировки поля домена.
     *
     * @return list<string>
     */
    abstract protected function allowedSorts(): array;
}
