<?php

use App\Models\User;

it('возвращает 404 с русским сообщением для несуществующего маршрута', function (): void {
    $response = $this->getJson('/api/does-not-exist');

    $response->assertStatus(404)
        ->assertJsonPath('meta.success', false)
        ->assertJsonPath('meta.error', 'Маршрут api/does-not-exist не найден.')
        ->assertJsonPath('data', []);
});

it('возвращает 405 с русским сообщением для неразрешённого метода', function (): void {
    $response = $this->postJson('/api/posts/my');

    $response->assertStatus(405)
        ->assertJsonPath('meta.success', false)
        ->assertJsonPath('meta.error', 'Метод POST не поддерживается этим маршрутом.')
        ->assertJsonPath('data', []);
});

it('возвращает тексты валидации на русском', function (): void {
    $response = $this->postJson('/api/register', [
        'email' => 'ivan@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(400)
        ->assertJsonPath('meta.success', false)
        ->assertJsonPath('meta.error', 'Поле name обязательно для заполнения.');
});

it('переводит ошибку способности токена на русский', function (): void {
    $user = User::factory()->create();
    $refreshToken = $user->createToken('refreshToken', ['refresh'])->plainTextToken;

    // refresh-токен не имеет способности access → действие запрещено
    $response = $this->withToken($refreshToken)->postJson('/api/logout');

    $response->assertStatus(403)
        ->assertJsonPath('meta.success', false)
        ->assertJsonPath('meta.error', 'Действие запрещено.');
});
