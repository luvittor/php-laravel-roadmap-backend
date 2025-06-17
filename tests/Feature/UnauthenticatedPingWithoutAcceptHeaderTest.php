<?php

namespace Tests\Feature;

use Tests\TestCase;

class UnauthenticatedPingWithoutAcceptHeaderTest extends TestCase
{
    public function test_ping_without_accept_header_returns_json_unauthenticated()
    {
        $response = $this->get('/api/ping');

        $response->assertStatus(401);
        $response->assertExactJson(['message' => 'Unauthenticated.']);
    }
}

