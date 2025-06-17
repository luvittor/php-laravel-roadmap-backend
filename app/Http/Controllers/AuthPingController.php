<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class AuthPingController extends Controller
{
    /**
     * Respond with a simple pong for authenticated users.
     */
    public function ping(): Response
    {
        return new Response('pong', 200, ['Content-Type' => 'text/plain']);
    }
}
