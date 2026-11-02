<?php

/**
 * Доступность документации API по конечным путям.
 */
it('открывает Swagger UI по пути /swagger', function (): void {
    $this->get('/swagger')
        ->assertOk()
        ->assertSee('SwaggerUIBundle', false);
});

it('не отвечает на старом пути /api/documentation', function (): void {
    $this->get('/api/documentation')->assertNotFound();
});

it('отдаёт спецификацию OpenAPI по пути /swagger.json', function (): void {
    // Спецификация генерируется на месте: у ревьювера её может не быть
    Artisan::call('l5-swagger:generate');

    $response = $this->get('/swagger.json');

    $response->assertOk();
    expect($response->json('openapi'))->toBe('3.0.0');
});

it('не отвечает на старом пути /docs', function (): void {
    Artisan::call('l5-swagger:generate');

    $this->get('/docs')->assertNotFound();
});
