<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function __construct(private AuthService $auth) {}

    /**
     * Register a new user and return token.
     *
     * @OA\Post(
     *     path="/api/v1/register",
     *     summary="Register a new user",
     *     description="Create a new user account and issue an API token.",
     *     tags={"auth"},
     *     security={{}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(ref="#/components/schemas/RegisterRequest")
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *
     *         @OA\JsonContent(ref="#/components/schemas/AuthTokenResponse")
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         ref="#/components/responses/ValidationError"
     *     )
     * )
     */
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        $result = $this->auth->register($data);

        return response()->json($result, Response::HTTP_CREATED);
    }

    /**
     * Authenticate user and return token.
     *
     * @OA\Post(
     *     path="/api/v1/login",
     *     summary="Authenticate a user",
     *     description="Issue a Sanctum API token for an existing user.",
     *     tags={"auth"},
     *     security={{}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(ref="#/components/schemas/LoginRequest")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Authentication successful",
     *
     *         @OA\JsonContent(ref="#/components/schemas/AuthTokenResponse")
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ErrorMessage")
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         ref="#/components/responses/ValidationError"
     *     )
     * )
     */
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $result = $this->auth->login($data);

        if ($result === null) {
            return response()->json(['message' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
        }

        return response()->json($result);
    }

    /**
     * Revoke current token.
     *
     * @OA\Post(
     *     path="/api/v1/logout",
     *     summary="Revoke the current token",
     *     tags={"auth"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Logout confirmation",
     *
     *         @OA\JsonContent(ref="#/components/schemas/MessageResponse")
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         ref="#/components/responses/Unauthenticated"
     *     )
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        $this->auth->logout($request->user());

        return response()->json(['message' => 'Logged out']);
    }

    /**
     * Return authenticated user profile.
     *
     * @OA\Get(
     *     path="/api/v1/user",
     *     summary="Retrieve the authenticated user profile",
     *     tags={"auth"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Authenticated user profile",
     *
     *         @OA\JsonContent(ref="#/components/schemas/UserProfile")
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         ref="#/components/responses/Unauthenticated"
     *     )
     * )
     */
    public function user(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }

    /**
     * Return authenticated pong message.
     *
     * @OA\Get(
     *     path="/api/v1/ping-auth",
     *     summary="Authenticated ping endpoint",
     *     tags={"auth"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Authenticated pong message",
     *
     *         @OA\JsonContent(ref="#/components/schemas/MessageResponse")
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         ref="#/components/responses/Unauthenticated"
     *     )
     * )
     */
    public function pingAuth(): JsonResponse
    {
        return response()->json(['message' => 'authenticated pong']);
    }
}
