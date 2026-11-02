<?php

use App\Exceptions\Api\ApiException;
use App\Http\Api\Middleware\TransformApiResponse;
use App\Http\Helpers\ResponseHelper;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Middleware abilities Sanctum для проверки типа токена
        $middleware->alias([
            'abilities' => CheckAbilities::class,
            'ability' => CheckForAnyAbility::class,
        ]);

        // Единая обёртка успешных ответов API: {meta: {success, error}, data: {...}}
        $middleware->appendToGroup('api', [
            TransformApiResponse::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Все исключения из API отдаём в формате JSON
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request, ?Throwable $e) => $request->is('api/*'),
        );

        // Ошибки валидации: текст первой ошибки в meta.error
        $exceptions->render(function (ValidationException $exception, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(
                    ResponseHelper::getResponseWrap(
                        false,
                        collect($exception->errors())->flatten()->first(),
                        [],
                    ),
                    400,
                );
            }
        });

        $exceptions->render(function (AuthorizationException|HttpException $exception, Request $request) {
            if ($request->is('api/*')) {
                // Сообщение политики приоритетнее дефолтного Laravel
                $message = filled($exception->getMessage())
                    ? __($exception->getMessage())
                    : __('This action is unauthorized.');

                $statusCode = method_exists($exception, 'getStatusCode')
                    ? $exception->getStatusCode()
                    : 403;

                return response()->json(
                    ResponseHelper::getResponseWrap(false, $message, []),
                    $statusCode,
                );
            }
        });

        // Нет валидного токена: признак invalid_access_token для клиента
        $exceptions->render(function (AuthenticationException $exception, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(
                    ResponseHelper::getNotAuthorizedResponseWrap(),
                    401,
                );
            }
        });

        // Доменные исключения API и прочие ошибки
        $exceptions->render(function (Throwable $exception, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(
                    ResponseHelper::getExceptionResponseWrap($exception),
                    $exception instanceof ApiException
                        ? $exception->getStatus()
                        : 500,
                );
            }
        });
    })->create();
