<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
{
    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        $user = Auth::user();
        $token = $user->createToken('API Token')->plainTextToken;
        
        return response()->json([
            'token' => $token,
            'user' => $user
        ]);
    }

    return response()->json(['error' => 'Unauthorized'], 401);
}
}
