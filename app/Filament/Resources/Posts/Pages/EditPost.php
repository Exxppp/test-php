<?php

namespace App\Filament\Resources\Posts\Pages;

use App\Filament\Resources\Posts\PostResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    /**
     * Заголовок вместо шаблона «Редактирование :label» Filament:
     * в русском падеже имя поля не совпадает с лейблом.
     */
    public function getTitle(): string
    {
        return 'Редактирование публикации';
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
