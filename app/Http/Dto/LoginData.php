<?php

namespace App\Http\Dto;

/**
 * Данные аутентификации по email и паролю.
 */
final readonly class LoginData
{
    public function __construct(
        public string $email,
        public string $password,
    ) {}
}
