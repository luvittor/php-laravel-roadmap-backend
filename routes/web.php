<?php

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

Route::get('/ping', function () {
    return new Response('pong', 200, ['Content-Type' => 'text/plain']);
});