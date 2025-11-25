<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SignInRequest;
use App\Http\Requests\Auth\SignUpRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\JWTGuard;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['signIn', 'signUp', 'refresh']]);
    }

    public function signIn(SignInRequest $request)
    {
        $credentials = $request->only(['email', 'password']);

        $accessToken = JWTAuth::attempt($credentials);
        //TODO lidar com o token expirado
        if (!$accessToken) {
            return response()->json(['message' => 'Invalid credentials'], 400);
        }

        return $this->respondWithToken($accessToken);
    }

    public function signUp(SignUpRequest $request)
    {
        $generatePassword = 'Usuario@flap123'; // TODO: gerar senha aleatória e enviar por email

        $request->merge(['password' => $generatePassword]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $generatePassword,
            'role_id' => $request->role_id,
        ]);
        if ($user) {
            return response()->json(['message' => 'User registered successfully'], 201);
        }

        return response()->json(['message' => 'User registration failed'], 500);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {

        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function signOut()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully signed out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        try {
            $newToken = JWTAuth::parseToken()->refresh();
            return $this->respondWithToken($newToken);
        } catch (TokenExpiredException $e) {
            return response()->json(['message' => 'Token expirado'], 401);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['message' => 'Token inválido'], 401);
        }
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
            // 'expires_in' => 60 * 60 * 24 // 1 day
            'expires_in' => 60 * 60 * 24 * 7 // 1 week
        ], 200);
    }
}
