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
            'meta' => ['success', 'error'],
            'data' => [
                'accessToken',
                'refreshToken',
                'user' => ['id', 'name', 'email'],
            ],
        ])
        ->assertJsonPath('meta.success', true)
        ->assertJsonPath('meta.error', '')
        ->assertJsonPath('data.user.email', 'ivan@example.com');

    expect(User::query()->where('email', 'ivan@example.com')->exists())->toBeTrue();

    // Выданный accessToken сразу работает
    $this->withToken($response->json('data.accessToken'))
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

    $response->assertStatus(409)
        ->assertJsonPath('meta.success', false)
        ->assertJsonPath('meta.error', 'Пользователь с таким email уже зарегистрирован.')
        ->assertJsonPath('data', []);
});

it('валидирует параметры регистрации', function (array $payload): void {
    $response = $this->postJson('/api/register', $payload);

    $response->assertStatus(400)
        ->assertJsonPath('meta.success', false)
        ->assertJsonPath('meta.error', fn (string $error): bool => filled($error));
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
        ->assertJsonPath('meta.success', true)
        ->assertJsonPath('data.user.id', $user->id);
});

it('не логинит с неверным паролем', function (): void {
    $user = User::factory()->create();

    $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => Str::random(9),
    ])->assertStatus(401)
        ->assertJsonPath('meta.success', false)
        ->assertJsonPath('meta.error', 'Неверный email или пароль.')
        ->assertJsonPath('data', []);
});
