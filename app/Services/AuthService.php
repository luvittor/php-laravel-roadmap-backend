<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function register(array $data): array
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $token = $user->createToken('api')->plainTextToken;

        $user->fresh();

        return [
            'token' => $token,
            'user' => $user->only(['id', 'email']),
        ];
    }

    public function login(array $data): array|null
    {
        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return null;
        }

        $token = $user->createToken('api')->plainTextToken;

        $user->fresh();

        return [
            'token' => $token,
            'user' => $user->only(['id', 'email']),
        ];
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }
}
