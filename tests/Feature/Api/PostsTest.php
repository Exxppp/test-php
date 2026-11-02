<?php

use App\Models\Post;
use App\Models\User;

it('требует access-токен для создания и чтения публикаций', function (): void {
    $this->getJson('/api/posts')
        ->assertUnauthorized()
        ->assertJsonPath('meta.invalid_access_token', true);

    $this->postJson('/api/posts', ['title' => 'Заголовок', 'text' => 'Текст'])
        ->assertUnauthorized();
});

it('публикует пост от имени владельца токена', function (): void {
    $user = User::factory()->create();
    $access = $user->createToken('accessToken', ['access'])->plainTextToken;

    $response = $this->withToken($access)->postJson('/api/posts', [
        'title' => 'Первая публикация',
        'text' => 'Текст первой публикации',
    ]);

    $response->assertCreated()
        ->assertJsonStructure([
            'meta' => ['success', 'error'],
            'data' => ['post' => ['id', 'title', 'text', 'author', 'created_at', 'updated_at']],
        ])
        ->assertJsonPath('meta.success', true)
        ->assertJsonPath('data.post.title', 'Первая публикация')
        ->assertJsonPath('data.post.author.id', $user->id);

    $post = Post::query()->first();
    expect($post->user_id)->toBe($user->id)
        ->and($post->title)->toBe('Первая публикация');
});

it('валидирует параметры публикации', function (): void {
    $user = User::factory()->create();
    $access = $user->createToken('accessToken', ['access'])->plainTextToken;

    $this->withToken($access)->postJson('/api/posts', ['title' => ''])
        ->assertStatus(400)
        ->assertJsonPath('meta.success', false)
        ->assertJsonPath('meta.error', fn (string $error): bool => filled($error));
});

it('не принимает refreshToken вместо accessToken', function (): void {
    $user = User::factory()->create();
    $refresh = $user->createToken('refreshToken', ['refresh'])->plainTextToken;

    $this->withToken($refresh)->postJson('/api/posts', ['title' => 'T', 'text' => 't'])
        ->assertForbidden()
        ->assertJsonPath('meta.success', false);
});

it('возвращает ленту порциями с мета-информацией', function (): void {
    $user = User::factory()->create();
    Post::factory()->count(5)->for($user, 'author')->create();
    $access = $user->createToken('accessToken', ['access'])->plainTextToken;

    $response = $this->withToken($access)->getJson('/api/posts?limit=2&offset=1');

    $response->assertOk()
        ->assertJsonStructure([
            'meta' => ['success', 'error'],
            'data' => [
                'posts' => [['id', 'title', 'text', 'author', 'created_at']],
                'pagination',
            ],
        ]);

    expect($response->json('data.posts'))->toHaveCount(2)
        ->and($response->json('data.pagination'))->toEqual([
            'total' => 5,
            'count' => 2,
            'limit' => 2,
            'offset' => 1,
            'sort' => 'date',
            'order' => 'desc',
        ]);
});

it('сортирует ленту по названию', function (): void {
    $user = User::factory()->create();
    $access = $user->createToken('accessToken', ['access'])->plainTextToken;

    Post::factory()->for($user, 'author')->create(['title' => 'Черновик']);
    Post::factory()->for($user, 'author')->create(['title' => 'Апрель']);
    Post::factory()->for($user, 'author')->create(['title' => 'Июнь']);

    $response = $this->withToken($access)->getJson('/api/posts?sort=title&order=asc');

    $response->assertOk();

    $titles = collect($response->json('data.posts'))->pluck('title');
    expect($titles->first())->toBe('Апрель')
        ->and($titles->last())->toBe('Черновик');
});

it('сортирует ленту по дате по возрастанию', function (): void {
    $user = User::factory()->create();
    $access = $user->createToken('accessToken', ['access'])->plainTextToken;

    Post::factory()->for($user, 'author')->create(['created_at' => now()->subDays(2)]);
    Post::factory()->for($user, 'author')->create(['created_at' => now()->subDay()]);

    $response = $this->withToken($access)->getJson('/api/posts?order=asc');

    $dates = collect($response->json('data.posts'))->pluck('created_at');
    expect($dates->first() <= $dates->last())->toBeTrue();
});

it('фильтрует ленту по дате от и до', function (): void {
    $user = User::factory()->create();
    $access = $user->createToken('accessToken', ['access'])->plainTextToken;

    Post::factory()->for($user, 'author')->create(['created_at' => now()->subDays(10)]);
    Post::factory()->for($user, 'author')->create(['created_at' => now()->subDays(2)]);
    Post::factory()->for($user, 'author')->create(['created_at' => now()]);

    $response = $this->withToken($access)
        ->getJson('/api/posts?date_from='.now()->subDays(5)->toDateString());

    expect($response->json('data.pagination.total'))->toBe(2);

    $response = $this->withToken($access)
        ->getJson('/api/posts?date_from='.now()->subDays(5)->toDateString().'&date_to='.now()->toDateString());

    expect($response->json('data.pagination.total'))->toBe(2);
});

it('валидирует параметры сортировки и фильтров', function (): void {
    $user = User::factory()->create();
    $access = $user->createToken('accessToken', ['access'])->plainTextToken;

    $this->withToken($access)->getJson('/api/posts?sort=id')->assertStatus(400);
    $this->withToken($access)->getJson('/api/posts?limit=200')->assertStatus(400);
    $this->withToken($access)->getJson('/api/posts?offset=-1')->assertStatus(400);
    $this->withToken($access)->getJson('/api/posts?date_from=не-дата')->assertStatus(400);
});

it('возвращает только мои публикации', function (): void {
    $alice = User::factory()->create();
    $bob = User::factory()->create();
    $access = $alice->createToken('accessToken', ['access'])->plainTextToken;

    Post::factory()->count(3)->for($alice, 'author')->create();
    Post::factory()->count(2)->for($bob, 'author')->create();

    $response = $this->withToken($access)->getJson('/api/posts/my');

    $response->assertOk();

    $authors = collect($response->json('data.posts'))->pluck('author.id')->unique();
    expect($response->json('data.posts'))->toHaveCount(3)
        ->and($response->json('data.pagination.total'))->toBe(3)
        ->and($authors->values()->all())->toEqual([$alice->id]);
});
