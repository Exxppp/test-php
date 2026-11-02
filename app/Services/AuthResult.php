<?php

namespace App\Services;

use App\Models\User;

/**
 * Результат аутентификации: пользователь и пара токенов.
 */
final class AuthResult
{
    public function __construct(
        public readonly User $user,
        public readonly string $accessToken,
        public readonly string $refreshToken,
    ) {}
}
