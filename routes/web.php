<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Response;

Route::get('/ping', function () {
    return new Response('pong', 200, ['Content-Type' => 'text/plain']);
});
