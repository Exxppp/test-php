<?php

namespace App\Filament\Resources\Posts\Pages;

use App\Filament\Resources\Posts\PostResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    /**
     * Заголовок вместо шаблона «Создать :label» Filament:
     * в русском падеже имя поля не совпадает с лейблом.
     */
    public function getTitle(): string
    {
        return 'Создать публикацию';
    }
}
