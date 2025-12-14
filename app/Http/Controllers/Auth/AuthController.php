<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'User registered successfully']);
    }

    public function login(Request $request)
{
    $credentials = $request->validate([
        'email'    => 'required|string|email',
        'password' => 'required|string',
    ]);

    if (!Auth::attempt($credentials)) {
        return response()->json(['message' => 'Email atau password salah'], 401);
    }

    $user = Auth::user();
    $userModel = User::find($user->id); // dapatkan user model dengan id
    $token = $userModel->createToken('auth_token')->plainTextToken;

    return response()->json([
        'user'  => $user,
        'token' => $token
    ], 200);
}

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logout berhasil'], 200);
    }

    // 🔹 Cek user login
    public function me(Request $request)
    {
        $user = $request->user();

        return $user;
    }
}
