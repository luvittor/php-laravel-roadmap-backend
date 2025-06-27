<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_returns_token_and_creates_user(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'secret123',
        ]);

        $response->assertCreated();
        $response->assertJsonStructure(['token']);
        $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
    }

    public function test_login_returns_token(): void
    {
        $user = User::factory()->create(['password' => Hash::make('secret123')]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'secret123',
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['token']);
    }

    public function test_user_and_logout_endpoints_require_authentication(): void
    {
        $this->getJson('/api/user')->assertUnauthorized();
        $this->postJson('/api/logout')->assertUnauthorized();
        $this->getJson('/api/ping-auth')->assertUnauthorized();
    }

    public function test_authenticated_routes_return_expected_responses(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('api')->plainTextToken;

        $headers = ['Authorization' => 'Bearer '.$token];

        $this->getJson('/api/user', $headers)
            ->assertOk()
            ->assertJsonPath('id', $user->id);

        $this->getJson('/api/ping-auth', $headers)
            ->assertOk()
            ->assertJson(['message' => 'authenticated pong']);

        $this->postJson('/api/logout', [], $headers)
            ->assertOk();

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
