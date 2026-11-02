<?php

namespace App\Http\Controllers\Api\Auth;

use App\Services\AuthService;
use Symfony\Component\HttpFoundation\Response;

final class LogoutController
{
    public function __invoke(AuthService $auth): Response
    {
        $auth->logout();

        return response()->noContent();
    }
}
