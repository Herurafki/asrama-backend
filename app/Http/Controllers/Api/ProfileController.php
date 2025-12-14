<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UpdatePasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Tampilkan profil user login.
     */
    public function show(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Update nama & email user.
     */
    public function update(UpdateProfileRequest $request, User $user)
    {
        // Pastikan hanya pemilik akun sendiri yang boleh update
        if ($request->user()->id !== $user->id) {
            return response()->json(['message' => 'Tidak diizinkan.'], 403);
        }

        $user->update($request->validated());

        return response()->json($user);
    }

    /**
     * Ubah password user.
     */
    public function updatePassword(UpdatePasswordRequest $request, User $user)
    {
        if ($request->user()->id !== $user->id) {
            return response()->json(['message' => 'Tidak diizinkan.'], 403);
        }

        // Verifikasi password lama
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Password lama tidak sesuai.'], 422);
        }

        $user->update(['password' => Hash::make($request->new_password)]);

        return response()->json(['message' => 'Password berhasil diperbarui.']);
    }
}
