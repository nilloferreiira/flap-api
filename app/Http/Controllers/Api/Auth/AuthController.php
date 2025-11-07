<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SignInRequest;
use App\Http\Requests\Auth\SignUpRequest;

class AuthController extends Controller
{
    public function signIn(SignInRequest $request)
    {
        // Authentication logic here
    }

    public function signUp(SignUpRequest $request)
    {
        return response()->json(['message' => 'User registered successfully']);
    }
}
