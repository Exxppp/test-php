<?php

namespace App\Exceptions\Api;

/**
 * Ошибка аутентификации: пользователь не найден или пароль неверный.
 */
final class InvalidCredentialsException extends ApiException
{
    protected int $status = 401;

    public function __construct()
    {
        parent::__construct('Неверный email или пароль.');
    }
}
