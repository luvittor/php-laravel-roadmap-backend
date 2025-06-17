<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticatedPingTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_ping()
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret'),
        ]);

        $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'secret',
        ])->assertStatus(200);

        $response = $this->get('/api/ping');

        $response->assertStatus(200);
        $response->assertSeeText('pong');
        $response->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
    }

    public function test_ping_requires_authentication()
    {
        $response = $this->get('/api/ping', [
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthenticated.']);
    }
}
