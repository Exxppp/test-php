<?php

namespace App\Http\Requests;

use App\Http\Dto\LoginData;

/**
 * Входящие параметры авторизации пользователя.
 */
class LoginRequest extends ApiRequest
{
    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function toDto(): LoginData
    {
        return new LoginData(
            email: $this->validated('email'),
            password: $this->validated('password'),
        );
    }
}
