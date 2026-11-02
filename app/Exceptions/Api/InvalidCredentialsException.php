<?php

namespace App\Exceptions\Api;

final class InvalidCredentialsException extends ApiException
{
    protected int $status = 401;

    public function __construct()
    {
        parent::__construct('Неверный email или пароль.');
    }
}
