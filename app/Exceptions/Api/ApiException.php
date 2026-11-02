<?php

namespace App\Exceptions\Api;

use Exception;
use Illuminate\Http\JsonResponse;

/**
 * Базовое доменное исключение API: отдаёт ошибку в едином JSON-формате.
 */
abstract class ApiException extends Exception
{
    protected int $status = 400;

    public function getStatus(): int
    {
        return $this->status;
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
        ], $this->status);
    }
}
