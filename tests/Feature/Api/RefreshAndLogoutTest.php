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
        ->assertJsonStructure(['accessToken', 'refreshToken', 'tokenType', 'user'])
        ->assertJsonPath('user.id', $user->id);

    // Старый refresh-токен ревокнут (ротация)
    forgetAuthGuards();
    $this->withToken($refresh)->postJson('/api/refresh')->assertUnauthorized();

    // Новый refreshToken нельзя использовать вместо accessToken
    forgetAuthGuards();
    $this->withToken($response->json('refreshToken'))
        ->getJson('/api/posts')
        ->assertForbidden();

    // Новая пара рабочая
    forgetAuthGuards();
    $this->withToken($response->json('accessToken'))->getJson('/api/posts')->assertOk();
});

it('не обновляет токены по accessToken', function (): void {
    $user = User::factory()->create();
    $access = $user->createToken('accessToken', ['access'], now()->addHour())->plainTextToken;

    $this->withToken($access)->postJson('/api/refresh')->assertForbidden();
});

it('требует токен для refresh', function (): void {
    $this->postJson('/api/refresh')->assertUnauthorized();
});

it('ревокает все токены при logout', function (): void {
    $user = User::factory()->create();
    $access = $user->createToken('accessToken', ['access'])->plainTextToken;
    $user->createToken('refreshToken', ['refresh']);

    $this->withToken($access)->postJson('/api/logout')->assertNoContent();

    expect($user->tokens()->count())->toBe(0);

    // Пользователь больше не аутентифицирован
    forgetAuthGuards();
    $this->withToken($access)->getJson('/api/posts')->assertUnauthorized();
});
