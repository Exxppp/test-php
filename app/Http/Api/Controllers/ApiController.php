<?php

namespace App\Http\Api\Controllers;

use Illuminate\Routing\Controller;

/**
 * Базовый контроллер API: общее описание OpenAPI (информация, сервер,
 * security-scheme и переиспользуемые схемы ответов).
 *
 * @OA\OpenApi(
 *     openapi="3.0.0",
 *
 *     @OA\Info(
 *         title="API «Блог»",
 *         version="1.0.0",
 *         description="REST API блога: аутентификация по паре токенов (access + refresh), публикация постов и порционная лента. Все ответы заключены в единую обёртку {meta, data}."
 *     ),
 *
 *     @OA\Server(
 *         url="/api/",
 *         description="Основной API сервер"
 *     ),
 *
 *     @OA\Components(
 *
 *         @OA\SecurityScheme(
 *             securityScheme="accessToken",
 *             type="apiKey",
 *             in="header",
 *             name="Authorization",
 *             description="Передайте токен в формате: Bearer {access_token}"
 *         ),
 *
 *         @OA\Schema(
 *             schema="Meta",
 *             type="object",
 *
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="error", type="string", nullable=true, example=""),
 *             @OA\Property(property="invalid_access_token", type="boolean", nullable=true, example=null)
 *         ),
 *
 *         @OA\Schema(
 *             schema="UserResource",
 *             type="object",
 *
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="name", type="string", example="Пользователь"),
 *             @OA\Property(property="email", type="string", example="user@blog.local"),
 *             @OA\Property(property="created_at", type="string", format="date-time")
 *         ),
 *
 *         @OA\Schema(
 *             schema="PostResource",
 *             type="object",
 *
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="title", type="string", example="Заголовок публикации"),
 *             @OA\Property(property="text", type="string", example="Текст публикации."),
 *             @OA\Property(property="author", ref="#/components/schemas/UserResource"),
 *             @OA\Property(property="created_at", type="string", format="date-time"),
 *             @OA\Property(property="updated_at", type="string", format="date-time")
 *         ),
 *
 *         @OA\Schema(
 *             schema="Pagination",
 *             type="object",
 *
 *             @OA\Property(property="total", type="integer", example=25),
 *             @OA\Property(property="count", type="integer", example=15),
 *             @OA\Property(property="limit", type="integer", example=15),
 *             @OA\Property(property="offset", type="integer", example=0),
 *             @OA\Property(property="sort", type="string", enum={"date", "title"}, example="date"),
 *             @OA\Property(property="order", type="string", enum={"asc", "desc"}, example="desc")
 *         ),
 *
 *         @OA\Schema(
 *             schema="AuthData",
 *             type="object",
 *
 *             @OA\Property(property="user", ref="#/components/schemas/UserResource"),
 *             @OA\Property(property="access_token", type="string"),
 *             @OA\Property(property="refresh_token", type="string")
 *         ),
 *
 *         @OA\Schema(
 *             schema="RefreshData",
 *             type="object",
 *
 *             @OA\Property(property="access_token", type="string"),
 *             @OA\Property(property="refresh_token", type="string")
 *         ),
 *
 *         @OA\Schema(
 *             schema="PostData",
 *             type="object",
 *
 *             @OA\Property(property="post", ref="#/components/schemas/PostResource")
 *         ),
 *
 *         @OA\Schema(
 *             schema="FeedData",
 *             type="object",
 *
 *             @OA\Property(
 *                 property="posts",
 *                 type="array",
 *
 *                 @OA\Items(ref="#/components/schemas/PostResource")
 *             ),
 *
 *             @OA\Property(property="pagination", ref="#/components/schemas/Pagination")
 *         ),
 *
 *         @OA\Schema(
 *             schema="RegisterResponse",
 *             type="object",
 *
 *             @OA\Property(property="meta", ref="#/components/schemas/Meta"),
 *             @OA\Property(property="data", ref="#/components/schemas/AuthData")
 *         ),
 *
 *         @OA\Schema(
 *             schema="LoginResponse",
 *             type="object",
 *
 *             @OA\Property(property="meta", ref="#/components/schemas/Meta"),
 *             @OA\Property(property="data", ref="#/components/schemas/AuthData")
 *         ),
 *
 *         @OA\Schema(
 *             schema="RefreshResponse",
 *             type="object",
 *
 *             @OA\Property(property="meta", ref="#/components/schemas/Meta"),
 *             @OA\Property(property="data", ref="#/components/schemas/RefreshData")
 *         ),
 *
 *         @OA\Schema(
 *             schema="LogoutResponse",
 *             type="object",
 *
 *             @OA\Property(property="meta", ref="#/components/schemas/Meta"),
 *             @OA\Property(property="data", type="array", maxItems=0,
 *
 *             @OA\Items(type="string")
 *         )
 *         ),
 *
 *         @OA\Schema(
 *             schema="StorePostResponse",
 *             type="object",
 *
 *             @OA\Property(property="meta", ref="#/components/schemas/Meta"),
 *             @OA\Property(property="data", ref="#/components/schemas/PostData")
 *         ),
 *
 *         @OA\Schema(
 *             schema="FeedResponse",
 *             type="object",
 *
 *             @OA\Property(property="meta", ref="#/components/schemas/Meta"),
 *             @OA\Property(property="data", ref="#/components/schemas/FeedData")
 *         ),
 *
 *         @OA\Schema(
 *             schema="ErrorResponse",
 *             type="object",
 *
 *             @OA\Property(property="meta", ref="#/components/schemas/Meta"),
 *             @OA\Property(property="data", type="array", maxItems=0,
 *
 *             @OA\Items(type="string")
 *         )
 *         )
 *     )
 * )
 */
abstract class ApiController extends Controller {}
