<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * @OA\Post(
 *     path="/api/login",
 *     tags={"Auth"},
 *     summary="Login",
 *     description="Login user and return JWT token",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email", "password"},
 *             @OA\Property(property="email", type="string", example="user@example.com"),
 *             @OA\Property(property="password", type="string", example="password123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Login successful",
 *         @OA\JsonContent(
 *             @OA\Property(property="user", type="object"),
 *             @OA\Property(property="token", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized"
 *     ),
 * )
 *
 * @OA\Post(
 *     path="/api/logout",
 *     tags={"Auth"},
 *     summary="Logout",
 *     description="Logout user and invalidate JWT token",
 *     @OA\Response(
 *         response=200,
 *         description="Logout successful",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Successfully logged out")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Failed to log out"
 *    )
 * )
 */
class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        try {
            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $token = JWTAuth::fromUser($user);
                return response()->json([
                    'user' => $user,
                    'token' => $token,
                ], 200);
            } else {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }

    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json(['message' => 'Successfully logged out'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to log out'], 500);
        }
    }
}
