<?php

namespace App\Http\Controllers\Api\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AdminManagementController extends Controller
{
    /**
     * Menampilkan daftar semua admin (selain superadmin itu sendiri)
     * GET /api/superadmin/manage-admins
     */
    public function index(): JsonResponse
    {
        // Ambil semua user dengan role 'admin'
        // Kita tidak menampilkan 'superadmin' atau 'alumni' di sini
        $admins = User::where('role', 'admin')->get();

        return response()->json([
            'success' => true,
            'data'    => $admins
        ]);
    }

    /**
     * Membuat akun admin baru
     * POST /api/superadmin/manage-admins
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'username' => 'required|string|max:50|unique:users,username',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        $admin = User::create([
            'username' => strtolower(trim($request->username)),
            'email'    => trim($request->email),
            'password' => Hash::make($request->password),
            'role'     => 'admin', // Paksa role menjadi admin
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Akun admin berhasil dibuat.',
            'data'    => $admin
        ], 201);
    }

    /**
     * Menampilkan detail satu akun admin
     * GET /api/superadmin/manage-admins/{id}
     */
    public function show($id): JsonResponse
    {
        $admin = User::where('role', 'admin')->find($id);

        if (!$admin) {
            return response()->json(['success' => false, 'message' => 'Admin tidak ditemukan.'], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $admin
        ]);
    }

    /**
     * Memperbarui data admin (hanya username/email)
     * PUT /api/superadmin/manage-admins/{id}
     */
    public function update(Request $request, $id): JsonResponse
    {
        $admin = User::where('role', 'admin')->find($id);

        if (!$admin) {
            return response()->json(['success' => false, 'message' => 'Admin tidak ditemukan.'], 404);
        }

        $request->validate([
            'username' => 'sometimes|required|string|max:50|unique:users,username,' . $id,
            'email'    => 'sometimes|required|email|max:255|unique:users,email,' . $id,
        ]);

        $adminData = [];
        if ($request->has('username')) $adminData['username'] = strtolower(trim($request->username));
        if ($request->has('email')) $adminData['email'] = trim($request->email);

        if (!empty($adminData)) {
            $admin->update($adminData);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data admin berhasil diperbarui.',
            'data'    => $admin
        ]);
    }

    /**
     * Menghapus akun admin
     * DELETE /api/superadmin/manage-admins/{id}
     */
    public function destroy($id): JsonResponse
    {
        $admin = User::where('role', 'admin')->find($id);

        if (!$admin) {
            return response()->json(['success' => false, 'message' => 'Admin tidak ditemukan.'], 404);
        }

        $admin->delete();

        return response()->json([
            'success' => true,
            'message' => 'Akun admin berhasil dihapus permanen.'
        ]);
    }

    /**
     * Fitur Tombol Nuklir: Reset Password Massal
     * POST /api/superadmin/manage-admins/mass-reset
     */
    public function massResetPassword(Request $request): JsonResponse
    {
        // Validasi opsional jika superadmin ingin menentukan password default
        $request->validate([
            'default_password' => 'nullable|string|min:8'
        ]);

        // Gunakan password dari request, atau gunakan default sistem
        $newPassword = $request->input('default_password', 'AdminPolije123!');
        $hashedPassword = Hash::make($newPassword);

        // Hanya mereset user dengan role 'admin'
        // Superadmin dan alumni aman dari operasi ini
        $affectedRows = User::where('role', 'admin')->update([
            'password' => $hashedPassword
        ]);

        return response()->json([
            'success' => true,
            'message' => "Password $affectedRows akun admin berhasil direset massal.",
            'default_password' => $newPassword
        ]);
    }
}
