<?php

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

    $this->actingAs($admin)->get('/admin')->assertOk();
});

it('redirect обычного пользователя (403) со страницы панели', function (): void {
    $regular = User::factory()->create();

    $this->actingAs($regular)->get('/admin')->assertForbidden();
});

it('показывает форму входа в админку', function (): void {
    $this->get('/admin/login')->assertOk();
});
