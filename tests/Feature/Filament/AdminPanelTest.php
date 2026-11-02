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
    // Сырые ключи переводов больше не всплывают в разметке
    $response->assertDontSee('filament-panels::');
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

it('называет страницы создания с русскими падежами', function (): void {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $this->get('/admin/users/create')
        ->assertOk()
        ->assertSee('Создать пользователя')
        ->assertSee('Создать и создать ещё')
        ->assertDontSee('Создать Пользователь')
        ->assertDontSee('Создать и Создать еще');

    $this->get('/admin/posts/create')
        ->assertOk()
        ->assertSee('Создать публикацию')
        ->assertDontSee('Создать Публикацию');
});

it('называет страницы редактирования с русскими падежами', function (): void {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);
    $user = User::factory()->create();

    $this->get('/admin/users/'.$user->id.'/edit')
        ->assertOk()
        ->assertSee('Редактирование пользователя')
        ->assertDontSee('Редактирование Пользователь');
});

it('рисует календарь Filament вместо нативного виджета браузера', function (): void {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    // Нативный input рисует браузер на языке интерфейса Chrome;
    // календарь Filament локализуется через APP_LOCALE
    expect($this->get('/admin/users/create')->getContent())
        ->not->toContain('datetime-local')
        ->and($this->get('/admin/posts')->getContent())
        ->not->toContain('datetime-local');
});

it('показывает форму входа в админку', function (): void {
    $this->get('/admin/login')->assertOk();
});
