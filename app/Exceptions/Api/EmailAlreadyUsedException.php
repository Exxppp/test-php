<?php

namespace App\Exceptions\Api;

/**
 * Ошибка регистрации: пользователь с таким email уже существует.
 */
final class EmailAlreadyUsedException extends ApiException
{
    protected int $status = 409;

    public function __construct()
    {
        parent::__construct('Пользователь с таким email уже зарегистрирован.');
    }
}
