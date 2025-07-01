<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\CreatesApplication;

use App\Models\User;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function authHeaders(User $user): array
    {
        $token = $user->createToken('api')->plainTextToken;
        return ['Authorization' => 'Bearer ' . $token];
    }
}
