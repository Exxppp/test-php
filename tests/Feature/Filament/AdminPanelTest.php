<?php

use App\Filament\Resources\Posts\PostResource;
use App\Filament\Resources\Users\UserResource;
use App\Models\User;

it('пускает в панель только администратора', function (): void {
    $admin = User::factory()->admin()->create();
    $regular = User::factory()->create();
    $panel = filament()->getPanel('admin');

    expect($admin->canAccessPanel($panel))->toBeTrue()
        ->and($regular->canAccessPanel($panel))->toBeFalse();
});

it('открывает панель админу после авторизации', function (): void {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->get('/admin');

    $response->assertOk();
    $response->assertSee('Панель управления');
    $response->assertDontSee('Инфопанель');
});

it('называет ресурсы по-русски во множественном числе', function (): void {
    expect(UserResource::getPluralModelLabel())->toBe('Пользователи')
        ->and(PostResource::getPluralModelLabel())->toBe('Публикации');
});

it('показывает страницы списков с русскими заголовками', function (): void {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $this->get('/admin/users')
        ->assertOk()
        ->assertSee('Пользователи')
        ->assertDontSee('Пользовательs');

    $this->get('/admin/posts')
        ->assertOk()
        ->assertSee('Публикации')
        ->assertDontSee('Публикацияs');
});

it('redirect обычного пользователя (403) со страницы панели', function (): void {
    $regular = User::factory()->create();

    $this->actingAs($regular)->get('/admin')->assertForbidden();
});

it('показывает форму входа в админку', function (): void {
    $this->get('/admin/login')->assertOk();
});
