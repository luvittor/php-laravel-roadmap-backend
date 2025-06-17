<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_and_logout()
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret')
        ]);

        $response = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'secret',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Logged in']);
        $this->assertAuthenticatedAs($user);

        $response = $this->get('/api/user');
        $response->assertStatus(200);
        $response->assertJsonPath('user.id', $user->id);

        $response = $this->post('/api/logout');
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Logged out']);
        $this->assertGuest();
    }

    public function test_login_returns_success_response()
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret'),
        ]);

        $response = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'secret',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Logged in']);
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret'),
        ]);

        $response = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401);
        $response->assertJson(['message' => 'Invalid credentials']);
        $this->assertGuest();
    }

    public function test_user_endpoint_returns_authenticated_user()
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret'),
        ]);

        $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'secret',
        ])->assertStatus(200);

        $response = $this->get('/api/user');

        $response->assertStatus(200);
        $response->assertJsonPath('user.id', $user->id);
    }

    public function test_logout_logs_out_user()
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret'),
        ]);

        $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'secret',
        ])->assertStatus(200);

        $response = $this->post('/api/logout');

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Logged out']);
        $this->assertGuest();
    }
}

