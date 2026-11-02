<?php

namespace App\Http\Requests;

use App\Http\Dto\CreatePostData;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Входящие параметры публикации поста в блог.
 */
class CreatePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

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
