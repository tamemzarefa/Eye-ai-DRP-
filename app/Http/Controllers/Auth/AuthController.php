<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * AuthController — Handles doctor authentication.
 *
 * Issues Sanctum API tokens on successful login.
 * Revokes the current token on logout.
 * Returns the authenticated user profile on /me.
 */
class AuthController extends Controller
{
    // ─── POST /api/v1/auth/login ──────────────────────────────────

    /**
     * Authenticate a doctor and issue a Sanctum token.
     *
     * @throws ValidationException  When credentials are invalid.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        /** @var array{email: string, password: string} $credentials */
        $credentials = $request->validated();

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Revoke previous tokens to enforce single-session logins
        $user->tokens()->delete();

        $token = $user->createToken(
            name: 'doctor-session',
            abilities: ['*'],
        )->plainTextToken;

        return response()->json([
            'message' => 'Login successful.',
            'token'   => $token,
            'user'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role,
            ],
        ], JsonResponse::HTTP_OK);
    }

    // ─── POST /api/v1/auth/logout ─────────────────────────────────

    /**
     * Revoke the current token (log out the doctor).
     */
    public function logout(Request $request): JsonResponse
    {
        /** @var \Laravel\Sanctum\PersonalAccessToken $token */
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }

    // ─── GET /api/v1/auth/me ──────────────────────────────────────

    /**
     * Return the currently authenticated doctor's profile.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'id'    => $user->id,
            'name'  => $user->name,
            'email' => $user->email,
            'role'  => $user->role,
        ]);
    }
}
