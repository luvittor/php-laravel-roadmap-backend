<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request): Response
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return response(
                ['message' => 'Logged in'],
                200,
                ['Content-Type' => 'application/json']
            );
        }

        return response(
            ['message' => 'Invalid credentials'],
            401,
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): Response
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response(
            ['message' => 'Logged out'],
            200,
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * Get the authenticated user.
     */
    public function user(Request $request): Response
    {
        return response(
            ['user' => $request->user()],
            200,
            ['Content-Type' => 'application/json']
        );
    }

}
