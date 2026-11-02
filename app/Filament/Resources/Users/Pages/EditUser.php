<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    /**
     * Заголовок вместо шаблона «Редактирование :label» Filament:
     * в русском падеже имя поля не совпадает с лейблом.
     */
    public function getTitle(): string
    {
        return 'Редактирование пользователя';
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
