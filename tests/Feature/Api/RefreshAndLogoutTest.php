<?php

use App\Models\User;

// Сброс кеша guard'ов между несколькими запросами в рамках одного теста:
// AuthManager кеширует RequestGuard, а тот — resolved user и токен.
function forgetAuthGuards(): void
{
    app('auth')->forgetGuards();
}

it('обновляет пару токенов по refreshToken', function (): void {
    $user = User::factory()->create();
    $access = $user->createToken('accessToken', ['access'], now()->addHour())->plainTextToken;
    $refresh = $user->createToken('refreshToken', ['refresh'], now()->addDays(30))->plainTextToken;

    $response = $this->withToken($refresh)->postJson('/api/refresh');

    $response->assertOk()
        ->assertJsonStructure([
            'meta' => ['success', 'error'],
            'data' => ['accessToken', 'refreshToken'],
        ])
        ->assertJsonPath('meta.success', true);

    // Старый refresh-токен ревокнут (ротация)
    forgetAuthGuards();
    $this->withToken($refresh)->postJson('/api/refresh')
        ->assertUnauthorized()
        ->assertJsonPath('meta.success', false)
        ->assertJsonPath('meta.invalid_access_token', true);

    // Новый refreshToken нельзя использовать вместо accessToken
    forgetAuthGuards();
    $this->withToken($response->json('data.refreshToken'))
        ->getJson('/api/posts')
        ->assertForbidden();

    // Новая пара рабочая
    forgetAuthGuards();
    $this->withToken($response->json('data.accessToken'))->getJson('/api/posts')->assertOk();
});

it('не обновляет токены по accessToken', function (): void {
    $user = User::factory()->create();
    $access = $user->createToken('accessToken', ['access'], now()->addHour())->plainTextToken;

    $this->withToken($access)->postJson('/api/refresh')
        ->assertForbidden()
        ->assertJsonPath('meta.success', false);
});

it('требует токен для refresh', function (): void {
    $this->postJson('/api/refresh')
        ->assertUnauthorized()
        ->assertJsonPath('meta.invalid_access_token', true);
});

it('ревокает все токены при logout', function (): void {
    $user = User::factory()->create();
    $access = $user->createToken('accessToken', ['access'])->plainTextToken;
    $user->createToken('refreshToken', ['refresh']);

    $this->withToken($access)->postJson('/api/logout')
        ->assertOk()
        ->assertJsonPath('meta.success', true)
        ->assertJsonPath('data', []);

    expect($user->tokens()->count())->toBe(0);

    // Пользователь больше не аутентифицирован
    forgetAuthGuards();
    $this->withToken($access)->getJson('/api/posts')
        ->assertUnauthorized()
        ->assertJsonPath('meta.invalid_access_token', true);
});
