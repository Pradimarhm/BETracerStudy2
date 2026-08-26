<?php

namespace App\Http\Controllers\Api\Alumni; // <-- PERBAIKAN NAMESPACE

use App\Http\Controllers\Controller;
use App\Http\Requests\Alumni\UpdateAlumniRequest;
use App\Http\Resources\AlumniResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;

class AlumniController extends Controller
{
    /**
     * Get current logged-in alumni profile.
     * GET /api/alumni/me
     */
    public function me(): JsonResponse
    {
        // Langsung load relasi dari Auth, tidak perlu query berulang
        $user = Auth::user();
        $user->load('alumni');

        if (!$user->alumni) {
            return response()->json([
                'success' => false,
                'message' => 'Profil alumni tidak ditemukan untuk akun ini.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => new AlumniResource($user->alumni)
        ]);
    }

    /**
     * Update current logged-in alumni profile.
     * PUT /api/alumni/update
     */
    public function update(UpdateAlumniRequest $request): JsonResponse
    {
        $user = Auth::user();
        $alumni = $user->alumni;

        if (!$alumni) {
            return response()->json(['success' => false, 'message' => 'Profil tidak ditemukan.'], 404);
        }

        DB::beginTransaction();
        try {
            // 1. Update Data Akun (Tabel Users)
            $userData = [];
            if ($request->has('email')) {
                $userData['email'] = trim($request->email);
            }
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            if (!empty($userData)) {
                $user->update($userData);
            }

            // 2. Update Data Profil (Tabel Alumnis)
            // Mengambil langsung dari $request untuk menghindari jebakan validated()
            $alumniData = [];
            if ($request->has('name')) $alumniData['name'] = $request->name;
            if ($request->has('nik')) $alumniData['nik'] = $request->nik;
            if ($request->has('npwp')) $alumniData['npwp'] = $request->npwp;
            if ($request->has('phone_number')) $alumniData['phone_number'] = $request->phone_number;
            if ($request->has('tahun_lulus')) $alumniData['tahun_lulus'] = $request->tahun_lulus;
            if ($request->has('kdpstmsmh')) $alumniData['kdpstmsmh'] = $request->kdpstmsmh;
            if ($request->has('status')) $alumniData['status'] = $request->status;
            if ($request->has('privacy_settings')) $alumniData['privacy_settings'] = $request->privacy_settings;

            if (!empty($alumniData)) {
                $alumni->update($alumniData);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Profil Anda berhasil diperbarui.',
                'data'    => new AlumniResource($alumni->load('user'))
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui profil: ' . $e->getMessage()
            ], 500);
        }
    }
}
