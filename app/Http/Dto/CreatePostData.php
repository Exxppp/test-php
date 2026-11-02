<?php

namespace App\Http\Dto;

/**
 * Данные публикации поста.
 */
final readonly class CreatePostData
{
    public function __construct(
        public string $title,
        public string $text,
    ) {}

    /**
     * Поля для создания публикации.
     *
     * @return array{title: string, text: string}
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'text' => $this->text,
        ];
    }
}
