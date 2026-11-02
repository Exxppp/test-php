<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    /**
     * Заголовок вместо шаблона «Создать :label» Filament:
     * в русском падеже имя поля не совпадает с лейблом.
     */
    public function getTitle(): string
    {
        return 'Создать пользователя';
    }
}
