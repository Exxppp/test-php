<?php

namespace App\Services;

use App\Exceptions\Api\EmailAlreadyUsedException;
use App\Exceptions\Api\InvalidCredentialsException;
use App\Http\Dto\LoginData;
use App\Http\Dto\RegisterData;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * Бизнес-логика аутентификации: регистрация, вход, refresh, logout.
 *
 * Токены выпускаются через Sanctum ($model->createToken()):
 * access_token — ability "access", refresh_token — ability "refresh".
 */
final class AuthService
{
    public const ACCESS_TOKEN_NAME = 'access_token';

    public const REFRESH_TOKEN_NAME = 'refresh_token';

    private const ACCESS_TOKEN_TTL_MINUTES = 60;

    private const REFRESH_TOKEN_TTL_DAYS = 30;

    /**
     * Регистрация пользователя и выпуск пары токенов.
     */
    public function register(RegisterData $data): AuthResult
    {
        if (User::query()->where('email', $data->email)->exists()) {
            throw new EmailAlreadyUsedException;
        }

        return $this->issueTokens(User::create($data->toArray()));
    }

    /**
     * Аутентификация пользователя по email и паролю.
     */
    public function login(LoginData $data): AuthResult
    {
        $user = User::query()->where('email', $data->email)->first();

        if ($user === null || ! Hash::check($data->password, $user->password)) {
            throw new InvalidCredentialsException;
        }

        return $this->issueTokens($user);
    }

    /**
     * Ротация токенов: предыдущий refresh-токен ревокается и выпускается новая пара.
     */
    public function refresh(): AuthResult
    {
        /** @var User $user */
        $user = auth()->user();
        /** @var PersonalAccessToken $refreshToken */
        $refreshToken = $user->currentAccessToken();

        $refreshToken->delete();

        return $this->issueTokens($user);
    }

    /**
     * Выход: ревок всех токенов пользователя.
     */
    public function logout(): void
    {
        /** @var User $user */
        $user = auth()->user();

        $user->tokens()->delete();
    }

    private function issueTokens(User $user): AuthResult
    {
        return new AuthResult(
            user: $user,
            accessToken: $user->createToken(
                self::ACCESS_TOKEN_NAME,
                ['access'],
                now()->addMinutes(self::ACCESS_TOKEN_TTL_MINUTES),
            )->plainTextToken,
            refreshToken: $user->createToken(
                self::REFRESH_TOKEN_NAME,
                ['refresh'],
                now()->addDays(self::REFRESH_TOKEN_TTL_DAYS),
            )->plainTextToken,
        );
    }
}
