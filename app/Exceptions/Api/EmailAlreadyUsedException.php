<?php

namespace App\Exceptions\Api;

final class EmailAlreadyUsedException extends ApiException
{
    protected int $status = 409;

    public function __construct()
    {
        parent::__construct('Пользователь с таким email уже зарегистрирован.');
    }
}
