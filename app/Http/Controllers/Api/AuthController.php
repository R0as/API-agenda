<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Annotations as OA;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Authenticate user and return JWT token",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="admin@admin.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Successful login"),
     *     @OA\Response(response=401, description="Invalid credentials")
     * )
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if (! $token = Auth::guard('api')->attempt($credentials)) {
            return response()->json(['error' => 'Credenciais inválidas'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * @OA\Get(
     *     path="/api/me",
     *     summary="Get the authenticated User",
     *     tags={"Authentication"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function me()
    {
        return response()->json(Auth::guard('api')->user());
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Log the user out (Invalidate the token)",
     *     tags={"Authentication"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Successfully logged out"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function logout()
    {
        Auth::guard('api')->logout();

        return response()->json(['message' => 'Deslogado com sucesso']);
    }

    /**
     * @OA\Post(
     *     path="/api/refresh",
     *     summary="Refresh a JWT token",
     *     tags={"Authentication"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Token refreshed"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function refresh()
    {
        return $this->respondWithToken(Auth::guard('api')->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60,
            'user' => Auth::guard('api')->user()
        ]);
    }
}
