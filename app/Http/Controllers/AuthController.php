<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            /** @var User $user */
            $user = Auth::user();
            $token = $user->createToken($request->header('User-Agent'))->plainTextToken;
    
            return response([
                'user' => $user,
                'token' => $token
            ]);
        }
        
        return response()->json([ "message" => __("auth.failed") ], 401);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
    }
}
