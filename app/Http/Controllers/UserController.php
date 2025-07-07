<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController
{
    public function index()
    {
        try {
            $cacheKey = 'users_index';
            $cacheDuration = 3600;

            $users = Cache::remember($cacheKey, $cacheDuration, function () {
                return User::paginate(10);
            });

            return response()->json([
                'message' => 'Usuarios obtenidos exitosamente',
                'users' => $users->items(),
                'total_pages' => $users->lastPage(),
                'total_users' => $users->total(),
                'current_page' => $users->currentPage(),
            ], 200);
        } catch (\Throwable $exc) {
            return response()->json([
                'message' => 'Error al recuperar usuarios',
                'error' => $exc->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => 'Datos inválidos', 'errors' => $validator->errors()], 422);
            }


            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'status' => true,
            ]);



            Cache::forget('users_index');

            return response()->json(['message' => 'Usuario creado exitosamente', 'user' => $user], 201);
        } catch (\Throwable $exc) {
            return response()->json([
                'message' => 'Error al crear usuario',
                'error' => $exc->getMessage(),
            ], 500);
        }
    }


    public function verifyEmail($token)
    {
        try {
            $user = User::where('email_verification_token', $token)->firstOrFail();
            $user->status = true;
            $user->email_verification_token = null;
            $user->email_verified_at = now();
            $user->save();

            return response()->json(['message' => 'Correo electrónico verificado exitosamente.'], 200);
        } catch (\Throwable $exc) {
            return response()->json([
                'message' => 'Error al verificar el correo electrónico',
                'error' => $exc->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $cacheKey = 'user_' . $id;
            $cacheDuration = 3600;

            $user = Cache::remember($cacheKey, $cacheDuration, function () use ($id) {
                return User::findOrFail($id);
            });

            return response()->json(['message' => 'Usuario obtenido exitosamente', 'user' => $user], 200);
        } catch (\Throwable $exc) {
            return response()->json([
                'message' => 'Error al recuperar usuario',
                'error' => $exc->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'string|max:255',
                'email' => 'string|email|max:255|unique:users,email,' . $id,
                'password' => 'string|min:8|nullable',
                'status' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => 'Datos inválidos', 'errors' => $validator->errors()], 422);
            }

            $user = User::findOrFail($id);
            $user->name = $request->name ?? $user->name;
            $user->email = $request->email ?? $user->email;
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
            $user->status = $request->status ?? $user->status;
            $user->save();

            Cache::forget('users_index');
            Cache::forget('user_' . $id);

            return response()->json(['message' => 'Usuario actualizado exitosamente', 'user' => $user], 200);
        } catch (\Throwable $exc) {
            return response()->json([
                'message' => 'Error al actualizar usuario',
                'error' => $exc->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            Cache::forget('users_index');
            Cache::forget('user_' . $id);

            return response()->json(['message' => 'Usuario eliminado exitosamente'], 200);
        } catch (\Throwable $exc) {
            return response()->json([
                'message' => 'Error al eliminar usuario',
                'error' => $exc->getMessage(),
            ], 500);
        }
    }
}
