<?php

namespace App\Exceptions\Api;

use Exception;

/**
 * Базовое доменное исключение API.
 *
 * Рендерится обработчиком исключений (bootstrap/app.php) в единую
 * обёртку {meta: {success: false, error: ...}, data: []}.
 */
abstract class ApiException extends Exception
{
    protected int $status = 400;

    public function getStatus(): int
    {
        return $this->status;
    }
}
