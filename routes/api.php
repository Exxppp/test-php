<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\RefreshController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Post\IndexController;
use App\Http\Controllers\Api\Post\MyController;
use App\Http\Controllers\Api\Post\StoreController;
use Illuminate\Support\Facades\Route;

// Аутентификация
Route::post('/register', RegisterController::class)->name('api.register');
Route::post('/login', LoginController::class)->name('api.login');

// Обновление пары токенов (Bearer: refreshToken)
Route::post('/refresh', RefreshController::class)
    ->middleware(['auth:sanctum', 'ability:refresh'])
    ->name('api.refresh');

// Методы, требующие accessToken (Bearer access)
Route::middleware(['auth:sanctum', 'ability:access'])->group(function (): void {
    Route::post('/logout', LogoutController::class)->name('api.logout');

    Route::post('/posts', StoreController::class)->name('api.posts.store');
    Route::get('/posts', IndexController::class)->name('api.posts.index');
    Route::get('/posts/my', MyController::class)->name('api.posts.my');
});
