<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Администратор админки
        User::factory()->admin()->create([
            'name' => 'Администратор',
            'email' => 'admin@blog.local',
        ]);

        // Обычный пользователь с публикациями
        User::factory()->create([
            'name' => 'Пользователь',
            'email' => 'user@blog.local',
        ])->each(function (User $user): void {
            Post::factory()->count(5)->for($user, 'author')->create();
        });

        // Ещё пользователи с публикациями для общей ленты
        User::factory()
            ->count(4)
            ->has(Post::factory()->count(5), 'posts')
            ->create();
    }
}
