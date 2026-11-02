<?php

use App\Models\User;
use Illuminate\Support\Str;

it('регистрирует пользователя и возвращает пару токенов', function (): void {
    $response = $this->postJson('/api/register', [
        'name' => 'Иван',
        'email' => 'ivan@example.com',
        'password' => 'password123',
    ]);

    $response->assertCreated()
        ->assertJsonStructure([
            'accessToken',
            'refreshToken',
            'tokenType',
            'user' => ['id', 'name', 'email'],
        ])
        ->assertJsonPath('tokenType', 'Bearer')
        ->assertJsonPath('user.email', 'ivan@example.com');

    expect(User::query()->where('email', 'ivan@example.com')->exists())->toBeTrue();

    // Выданный accessToken сразу работает
    $this->withToken($response->json('accessToken'))
        ->getJson('/api/posts')
        ->assertOk();
});

it('не регистрирует второй аккаунт с тем же email', function (): void {
    User::factory()->create(['email' => 'ivan@example.com']);

    $response = $this->postJson('/api/register', [
        'name' => 'Дубликат',
        'email' => 'ivan@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(409)->assertJson(['message' => 'Пользователь с таким email уже зарегистрирован.']);
});

it('валидирует параметры регистрации', function (array $payload): void {
    $this->postJson('/api/register', $payload)->assertStatus(422);
})->with(function (): array {
    return [
        'без имени' => [['email' => 'a@example.com', 'password' => 'password123']],
        'некорректный email' => [['name' => 'Иван', 'email' => 'не-email', 'password' => 'password123']],
        'короткий пароль' => [['name' => 'Иван', 'email' => 'a@example.com', 'password' => '123']],
    ];
});

it('логинит пользователя по email и паролю', function (): void {
    $user = User::factory()->create();

    $response = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertOk()
        ->assertJsonStructure(['accessToken', 'refreshToken', 'tokenType', 'user' => ['id', 'name', 'email']])
        ->assertJsonPath('user.id', $user->id);
});

it('не логинит с неверным паролем', function (): void {
    $user = User::factory()->create();

    $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => Str::random(9),
    ])->assertStatus(401)->assertJson(['message' => 'Неверный email или пароль.']);
});
