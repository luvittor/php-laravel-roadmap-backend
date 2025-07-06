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
        $email = 'john@example.com';

        $response = $this->postJson('/api/v1/register', [
            'name' => 'John Doe',
            'email' => $email,
            'password' => 'secret123',
        ]);

        $response->assertCreated();
        $response->assertJsonStructure(['token', 'user' => ['id', 'email']]);
        $this->assertDatabaseHas('users', ['email' => $email]);
        $this->assertEquals($email, $response->json('user.email'));
    }

    public function test_login_returns_token(): void
    {
        $user = User::factory()->create(['password' => Hash::make('secret123')]);

        $response = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'secret123',
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['token', 'user' => ['id', 'email']]);
        $this->assertEquals($user->email, $response->json('user.email'));
    }

    public function test_login_returns_error_for_invalid_credentials(): void
    {
        $user = User::factory()->create(['password' => Hash::make('secret123')]);

        $response = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401);
        $response->assertExactJson(['message' => 'Invalid credentials']);
    }

    public function test_register_validation_errors_are_returned_for_invalid_data(): void
    {
        $response = $this->postJson('/api/v1/register', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_login_validation_errors_are_returned_for_invalid_data(): void
    {
        $response = $this->postJson('/api/v1/login', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_user_and_logout_endpoints_require_authentication(): void
    {
        $this->getJson('/api/v1/user')->assertUnauthorized();
        $this->postJson('/api/v1/logout')->assertUnauthorized();
        $this->getJson('/api/v1/ping-auth')->assertUnauthorized();
    }

    public function test_authenticated_routes_return_expected_responses(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('api')->plainTextToken;

        $headers = ['Authorization' => 'Bearer '.$token];

        $this->getJson('/api/v1/user', $headers)
            ->assertOk()
            ->assertJsonPath('id', $user->id);

        $this->getJson('/api/v1/ping-auth', $headers)
            ->assertOk()
            ->assertJson(['message' => 'authenticated pong']);

        $this->postJson('/api/v1/logout', [], $headers)
            ->assertOk();

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_routes_return_unauthorized_for_invalid_token(): void
    {
        $headers = ['Authorization' => 'Bearer invalid'];

        $this->getJson('/api/v1/user', $headers)
            ->assertUnauthorized()
            ->assertExactJson(['message' => 'Unauthenticated.']);

        $this->postJson('/api/v1/logout', [], $headers)
            ->assertUnauthorized()
            ->assertExactJson(['message' => 'Unauthenticated.']);

        $this->getJson('/api/v1/ping-auth', $headers)
            ->assertUnauthorized()
            ->assertExactJson(['message' => 'Unauthenticated.']);
    }
}
