<?php

namespace App\Http\Helpers;

use App\Exceptions\Api\ApiException;
use App\Http\Resources\UserResource;
use App\Services\AuthResult;
use App\Services\AuthService;
use Illuminate\Validation\ValidationException;
use Throwable;

final class ResponseHelper
{
    /**
     * Единая сборка ответа аутентификации: пользователь и пара токенов.
     */
    public static function authPayload(AuthResult $result): array
    {
        return ['user' => new UserResource($result->user)] + self::tokenPayload($result);
    }

    /**
     * Пара токенов без пользователя (для refresh). Ключи payload'а
     * совпадают с именами токенов из AuthService.
     */
    public static function tokenPayload(AuthResult $result): array
    {
        return [
            AuthService::ACCESS_TOKEN_NAME => $result->accessToken,
            AuthService::REFRESH_TOKEN_NAME => $result->refreshToken,
        ];
    }

    /**
     * Единая обёртка ответов API: {meta: {success, error}, data: {...}}.
     */
    public static function getResponseWrap(bool $success, string $error, array $data): array
    {
        return [
            'meta' => [
                'success' => $success,
                'error' => $error,
            ],
            'data' => $data,
        ];
    }

    /**
     * Обёртка для ответа об отсутствии валидного access-токена.
     */
    public static function getNotAuthorizedResponseWrap(): array
    {
        return [
            'meta' => [
                'success' => false,
                'invalid_access_token' => true,
                'error' => __('Не авторизован.'),
            ],
            'data' => [],
        ];
    }

    /**
     * Обёртка исключения. Для клиентских ошибок (валидация, доменные
     * исключения со статусом 200) отладочная информация не добавляется,
     * даже если включён режим debug.
     */
    public static function getExceptionResponseWrap(Throwable $exception): array
    {
        $data = [];
        $isClientException = $exception instanceof ValidationException
            || ($exception instanceof ApiException && $exception->getStatus() === 200);
        if (config('app.debug') && ! $isClientException) {
            $data = [
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => self::parseTrace($exception->getTrace()),
            ];
        }

        return self::getResponseWrap(
            success: false,
            error: $exception->getMessage(),
            data: $data,
        );
    }

    /**
     * Форматированный стек-трейс для отладочных ответов.
     *
     * @param  array<int, array<string, mixed>>  $trace
     * @return list<string>
     */
    public static function parseTrace(array $trace): array
    {
        return array_map(static function (array $elem): string {
            return ($elem['file'] ?? '').'->'.($elem['function'] ?? '').' line: '.($elem['line'] ?? '');
        }, $trace);
    }
}
