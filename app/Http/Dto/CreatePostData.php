<?php

namespace App\Http\Dto;

final readonly class CreatePostData
{
    public function __construct(
        public string $title,
        public string $text,
    ) {}

    /** @return array{title: string, text: string} */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'text' => $this->text,
        ];
    }
}
