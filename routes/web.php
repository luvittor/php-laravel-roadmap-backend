<?php

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

Route::get('/ping', function () {
    return new Response('pong', 200, ['Content-Type' => 'text/plain']);
});

Route::get('/openapi.yaml', function () {
    $path = base_path('docs/openapi/roadmap.yaml');

    abort_unless(file_exists($path), 404);

    return response()->file($path, [
        'Content-Type' => 'application/yaml',
    ]);
})->name('docs.openapi');

Route::view('/docs/api', 'swagger')->name('docs.api');
