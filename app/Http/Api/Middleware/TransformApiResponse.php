<?php

namespace App\Http\Api\Middleware;

use App\Http\Helpers\ResponseHelper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Заворачивает успешные ответы API в единую обёртку:
 * {meta: {success, error}, data: {...}}.
 *
 * Ответы с исключением рендерит обработчик исключений (bootstrap/app.php).
 */
class TransformApiResponse
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        if (! empty($response->exception)) {
            return $response;
        }

        // StreamedResponse и BinaryFileResponse нельзя заворачивать
        // в JSON-обёртку: setContent запрещён
        if ($response instanceof BinaryFileResponse || $response instanceof StreamedResponse) {
            return $response;
        }

        $content = json_decode($response->getContent(), true) ?? [];

        $response->setContent(json_encode(
            ResponseHelper::getResponseWrap($response->isSuccessful(), '', $content)
        ));

        return $response;
    }
}
