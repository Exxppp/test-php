<?php

namespace App\Http\Requests;

use App\Http\Dto\CreatePostData;

class CreatePostRequest extends ApiRequest
{
    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'text' => ['required', 'string'],
        ];
    }

    public function toDto(): CreatePostData
    {
        return new CreatePostData(
            title: $this->validated('title'),
            text: $this->validated('text'),
        );
    }
}
