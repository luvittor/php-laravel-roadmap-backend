<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PingTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/ping');

        $response->assertStatus(200);
        $response->assertSeeText('pong');
        $response->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
    }
}
