# Blog API (Backend: PHP / Laravel)

API и админка для мобильного приложения «Блог»: регистрация и вход пользователей с парой токенов (access + refresh), публикация постов, порционная выдача общей ленты и «моих публикаций» с сортировками и фильтрацией, плюс админ-панель Filament.


## Стек

- Laravel 12, PHP 8.4, Composer
- MySQL 8+ (миграции обязательны)
- Laravel Sanctum — `access_token` (60 мин, ability `access`) + `refresh_token` (30 дней, ability `refresh`), выпускаются через `$user->createToken()`
- Filament 4 — админка `/admin`
- Pest — автотесты

## Установка и запуск

```bash
composer install
cp .env.example .env        # при первом запуске; затем php artisan key:generate
# настрать БД в .env: DB_CONNECTION=mysql, DB_DATABASE=blog ...
mysql -uroot -e "CREATE DATABASE IF NOT EXISTS blog CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
php artisan migrate --seed
php artisan serve           # http://127.0.0.1:8000
```

Аккаунты из сидера: админ `admin@blog.local / password`, пользователь `user@blog.local / password`.

## Админка

`http://127.0.0.1:8000/admin` — вход по email и паролю (форма Filament). В панель допускаются только пользователи с `is_admin = true` (`User::canAccessPanel()`). Разделы: **Пользователи** и **Публикации** — стандартный CRUD с поиском, сортировкой и фильтром по дате (публикации).

## API

Формат ответов — единая JSON-обёртка (совместима с мобильными клиентами):

```json
{
  "meta": { "success": true, "error": "" },
  "data": { "именованный ключ": { } }
}
```

Регистрация/вход: `data: {user, access_token, refresh_token}`; ротация (refresh) — только `data: {access_token, refresh_token}`.
- ошибки (401/404/409/400): `meta.success = false`, текст — в `meta.error`, `data: []`;
- при 401 дополнительно `meta.invalid_access_token = true` — сигнал клиенту перелогиниться;
- ошибки валидации — HTTP 400, в `meta.error` текст первой ошибки;
- списки порционно: `data.posts` (массив постов; без `pagination` — клиент определяет конец списка по количеству полученных записей).

| Метод | URL | Описание |
|---|---|---|
| POST | `/api/register` | Регистрация: `name`, `email`, `password` → пара токенов |
| POST | `/api/login` | Вход: `email`, `password` → пара токенов |
| POST | `/api/refresh` | Bearer: **refreshToken** → новая пара токенов (ротация) |
| POST | `/api/logout` | Bearer: accessToken → 200, ревок всех токенов |
| POST | `/api/posts` | Bearer: accessToken; тело `title`, `text` → публикация поста |
| GET | `/api/posts` | Лента всех публикаций (порционно) |
| GET | `/api/posts/my` | Публикации текущего пользователя (порционно) |

Параметры `GET /api/posts` и `GET /api/posts/my`:

- `sort` — `date` (по умолчанию) или `title`
- `order` — `asc` или `desc` (по умолчанию `desc`)
- `limit` — размер порции, 1–100 (по умолчанию 15, необязательный)
- `offset` — смещение для порционной загрузки (необязательный)
- `date_from`, `date_to` — фильтр по дате публикации, `YYYY-MM-DD` (необязательные)

Примеры:

```bash
# Регистрация
curl -s -X POST http://127.0.0.1:8000/api/register \
  -H 'Accept: application/json' \
  -d '{"name":"Иван","email":"ivan@example.com","password":"password123"}'

# Вход (используйте accessToken в заголовке Authorization ниже)
curl -s -X POST http://127.0.0.1:8000/api/login \
  -H 'Accept: application/json' \
  -d '{"email":"user@blog.local","password":"password"}'

# Публикация поста
curl -s -X POST http://127.0.0.1:8000/api/posts \
  -H 'Accept: application/json' \
  -H 'Authorization: Bearer <accessToken>' \
  -d '{"title":"Заголовок","text":"Текст публикации"}'

# Лента: сортировка по названию по возрастанию, порция 10
curl -s 'http://127.0.0.1:8000/api/posts?sort=title&order=asc&limit=10&offset=0' \
  -H 'Accept: application/json' -H 'Authorization: Bearer <accessToken>'
```

Postman-коллекция: [`postman/blog-api.postman_collection.json`](postman/blog-api.postman_collection.json) — токены сохраняются в переменные коллекции автоматически.

## Архитектура (тонкие контроллеры)

- `app/Http/Controllers/Api` — контроллеры-одноклассники: только валидация → сервис → ответ
- `app/Http/Requests` — валидация входящих параметров; `toDto()` транслирует validated в DTO. Базы: `ApiRequest` (общая точка всех запросов), `ListRequest` (листовые запросы: правила пагинации `limit`/`offset` и сортировки `sort`/`order` описаны один раз; наследник задаёт только `allowedSorts()` и доменные фильтры)
- `app/Http/Dto` — DTO-носители (`RegisterData`, `LoginData`, `CreatePostData`, `ListPostData`)
- `app/Services` — бизнес-логика и обработка ошибок (`AuthService`, `PostService`)
- `app/Http/Resources` — сериализация ответов (`UserResource`, `PostResource`)
- `app/Exceptions/Api` — доменные исключения (401/409); единый JSON-рендер в обёртке — обработчик исключений в `bootstrap/app.php`
- `app/Http/Api/Middleware/TransformApiResponse` — middleware-обёртка успешных ответов API в `{meta, data}`
- `app/Http/Helpers/ResponseHelper` — конструктор обёрток (`getResponseWrap`, `getNotAuthorizedResponseWrap`, `getExceptionResponseWrap`)
- JSON принудительно включён для `api/*` (`shouldRenderJsonWhen` в `bootstrap/app.php`)
- `app/Http/Api/Controllers/ApiController` + аннотации `@OA\...` в контроллерах — Swagger-документация (l5-swagger)

## Swagger

Swagger UI: [`/swagger`](http://127.0.0.1:8000/swagger) (при запущенном `php artisan serve`), JSON-спека: `GET /docs`.

Аннотации описаны в PHPDoc контроллеров; общие схемы обёртки (`Meta`, `AuthData`, `FeedData`, `ErrorResponse` и др.) — в `app/Http/Api/Controllers/ApiController.php`. После правки аннотаций перегенерировать:

```bash
php artisan l5-swagger:generate
```

## Тесты

```bash
php artisan test
```

25 Pest-тестов: аутентификация (регистрация/вход/валидация), ротация и ревок токенов, публикация постов, лента (limit/offset/sort/order/date_from/date_to), мои публикации, контроль доступа к админке Filament.