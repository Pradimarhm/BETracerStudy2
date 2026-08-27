<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminProfileController extends Controller
{
    /**
     * Get current logged-in admin profile.
     * GET /api/admin/me
     */
    public function me(): JsonResponse
    {
        // Langsung kembalikan data user yang sedang login
        return response()->json([
            'success' => true,
            'data'    => Auth::user()
        ]);
    }

    /**
     * Update current logged-in admin profile.
     * PUT /api/admin/update
     */
    public function update(Request $request): JsonResponse
    {
        $user = Auth::user();

        // Validasi, pastikan ID yang dikecualikan dari unique adalah ID miliknya sendiri
        $request->validate([
            'username' => 'sometimes|required|string|max:50|unique:users,username,' . $user->id,
            'email'    => 'sometimes|required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
        ]);

        $userData = [];
        if ($request->has('username')) $userData['username'] = strtolower(trim($request->username));
        if ($request->has('email')) $userData['email'] = trim($request->email);
        if ($request->filled('password')) $userData['password'] = Hash::make($request->password);

        if (!empty($userData)) {
            $user->update($userData);
        }

        return response()->json([
            'success' => true,
            'message' => 'Profil Anda berhasil diperbarui.',
            'data'    => $user
        ]);
    }
}
