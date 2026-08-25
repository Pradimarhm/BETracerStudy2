<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;

// request alumni untuk admin
use App\Http\Requests\Alumni\Admin\{
    ImportAlumniRequest,
    StoreAlumniByAdminRequest,
    UpdateAlumniByAdminRequest
};

use App\Http\Resources\AlumniResource;
use App\Models\Alumni;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminAlumniController extends Controller
{
    /**
     * Display a listing of alumni with search & filtering via URL Query Parameters.
     * GET /api/admin/alumni?search=...&status=...&tahunLulus=...&sortOrder=terbaru
     */
    public function index(Request $request): JsonResponse
    {
        $query = Alumni::with('user');

        // 1. Filtering Search (Nama, NIM, atau Email)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('email', 'like', "%{$search}%")
                            ->orWhere('username', 'like', "%{$search}%");
                    });
            });
        }

        // 2. Filtering Status Pekerjaan
        if ($request->filled('status') && $request->status !== 'ALL') {
            $query->where('status', $request->status);
        }

        // 3. Filtering Tahun Lulus
        if ($request->filled('tahunLulus') && $request->tahunLulus !== 'ALL') {
            $query->where('tahun_lulus', $request->tahunLulus);
        }

        // 4. Sorting
        $sortOrder = $request->get('sortOrder', 'terbaru');
        $query->orderBy('id', $sortOrder === 'terbaru' ? 'desc' : 'asc');

        $alumni = $query->get();

        return response()->json([
            'success' => true,
            'message' => 'Data alumni berhasil dimuat.',
            'data' => AlumniResource::collection($alumni)
        ]);
    }

    /**
     * Store a newly created alumni and its user account.
     * POST /api/admin/alumni
     */
    public function store(StoreAlumniByAdminRequest $request): JsonResponse
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            // 1. Buat Akun (Tabel Users)
            $user = User::create([
                'username' => strtolower(trim($validated['username'])),
                'email'    => trim($validated['email']),
                'password' => Hash::make($validated['password']),
                'role'     => 'alumni', // Paksa role menjadi alumni
            ]);

            // 2. Buat Profil Alumni (Tabel Alumnis)
            $alumni = Alumni::create([
                'user_id' => $user->id,
                'nim'     => strtoupper(trim($validated['username'])),
                'name'    => $validated['name'], // <-- UBAH JADI INI
                'status'  => 'Belum Mengisi',
                'privacy_settings' => ['show_phone' => true],
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Akun dan data alumni berhasil dibuat.',
                // Kembalikan data lengkap dengan relasi user untuk dirender di tabel frontend
                'data'    => new AlumniResource($alumni->load('user'))
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat alumni: Sistem database menolak transaksi. Detail: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display detail of a specific alumni by Alumni ID.
     * GET /api/admin/alumni/{id}
     */
    public function show($id): JsonResponse
    {
        $alumni = Alumni::with('user')->find($id);

        if (!$alumni) {
            return response()->json([
                'success' => false,
                'message' => 'Data alumni tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => new AlumniResource($alumni)
        ]);
    }

    /**
     * Update existing alumni profile by Admin.
     * PUT /api/admin/alumni/{id}
     */
    public function update(UpdateAlumniByAdminRequest $request, $id): JsonResponse
    {
        $alumni = Alumni::with('user')->find($id);

        if (!$alumni) {
            return response()->json(['success' => false, 'message' => 'Data alumni tidak ditemukan.'], 404);
        }

        $validated = $request->validated();

        DB::beginTransaction();
        try {
            // 1. Update Data User (Tabel Users)
            $userData = [];
            if (isset($validated['username'])) $userData['username'] = strtolower(trim($validated['username']));
            if (isset($validated['email'])) $userData['email'] = trim($validated['email']);
            if (!empty($validated['password'])) $userData['password'] = Hash::make($validated['password']);

            if (!empty($userData) && $alumni->user) {
                $alumni->user->update($userData);
            }

            // 2. Update Data Profil (Tabel Alumnis)
            $alumniData = [];
            if (isset($validated['name'])) $alumniData['name'] = $validated['name'];
            if (isset($validated['nim'])) $alumniData['nim'] = $validated['nim']; // Ambil dari input NIM yang sebenarnya
            if (isset($validated['nik'])) $alumniData['nik'] = $validated['nik'];
            if (isset($validated['npwp'])) $alumniData['npwp'] = $validated['npwp'];
            if (isset($validated['status'])) $alumniData['status'] = $validated['status'];
            if (isset($validated['tahun_lulus'])) $alumniData['tahun_lulus'] = $validated['tahun_lulus'];
            if (isset($validated['phone_number'])) $alumniData['phone_number'] = $validated['phone_number'];

            $alumni->update($alumniData);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data alumni berhasil diperbarui.',
                'data'    => new AlumniResource($alumni->load('user'))
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui data: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Bulk import alumni from CSV. Supports both ',' and ';' delimiters.
     * POST /api/admin/alumni/import
     */
    public function import(ImportAlumniRequest $request): JsonResponse
    {
        $file = $request->file('file');

        $handle = fopen($file->getRealPath(), 'r');

        // 1. DETEKSI OTOMATIS PEMISAH KOLOM (, ATAU ;)
        // Ambil baris pertama sebagai string untuk dianalisis
        $firstLine = fgets($handle);
        // Jika ada titik koma di baris pertama, gunakan ;, jika tidak gunakan ,
        $delimiter = strpos($firstLine, ';') !== false ? ';' : ',';

        // Kembalikan kursor pembacaan file ke paling awal
        rewind($handle);

        // 2. BACA DATA MENGGUNAKAN DELIMITER YANG DITEMUKAN
        // Membaca baris pertama (Header)
        $header = fgetcsv($handle, 1000, $delimiter);

        $rowCount = 0;

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                // Lewati baris kosong
                if (empty(array_filter($row))) continue;

                $name     = trim($row[0] ?? '');
                $username = strtolower(trim($row[1] ?? ''));
                $email    = trim($row[2] ?? '');
                $password = trim($row[3] ?? '');

                // Validasi manual jika ada data yang kosong di tengah baris
                if (!$name || !$username || !$email || !$password) {
                    throw new \Exception("Format data tidak lengkap pada baris ke-" . ($rowCount + 2));
                }

                // Cek duplikasi manual agar tidak terjadi SQL Error yang meledak
                if (User::where('email', $email)->orWhere('username', $username)->exists()) {
                    throw new \Exception("Username '$username' atau Email '$email' sudah terdaftar.");
                }

                // Create User
                $user = User::create([
                    'username' => $username,
                    'email'    => $email,
                    'password' => Hash::make($password),
                    'role'     => 'alumni',
                ]);

                // Create Profil
                Alumni::create([
                    'user_id' => $user->id,
                    'nim'     => strtoupper($username),
                    'name'    => $name,
                    'status'  => 'Belum Mengisi',
                    'privacy_settings' => ['show_phone' => true],
                ]);

                $rowCount++;
            }

            fclose($handle);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "$rowCount data alumni berhasil diimpor."
            ]);
        } catch (\Exception $e) {
            fclose($handle);
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Proses impor digagalkan. Kesalahan: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Export filtered alumni data to CSV with all database fields.
     * GET /api/admin/alumni/export?search=...&status=...&tahunLulus=...
     */
    public function export(Request $request)
    {
        $query = Alumni::with('user');

        // 1. Aplikasikan filter search & filtering
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('email', 'like', "%{$search}%")
                            ->orWhere('username', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status') && $request->status !== 'ALL') {
            $query->where('status', $request->status);
        }

        if ($request->filled('tahunLulus') && $request->tahunLulus !== 'ALL') {
            $query->where('tahun_lulus', $request->tahunLulus);
        }

        $alumni = $query->get();

        // 2. Siapkan Header HTTP untuk Download CSV
        $fileName = 'data_alumni_lengkap_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        // 3. Mapping Seluruh Kolom Sesuai Struktur Database
        $callback = function () use ($alumni) {
            $file = fopen('php://output', 'w');

            // Baris 1: Judul Kolom (Sesuai Foto DB)
            fputcsv($file, [
                'ID Alumni',
                'NIM',
                'NIK',
                'NPWP',
                'Nama Lengkap',
                'Username',
                'Email',
                'No. HP',
                'Tahun Lulus',
                'Kode Prodi (KDPSTMSMH)',
                'Status Pekerjaan'
            ]);

            // Baris 2 dst: Data Baris
            foreach ($alumni as $row) {
                fputcsv($file, [
                    $row->id,
                    $row->nim ?? '-',
                    $row->nik ?? '-',
                    $row->npwp ?? '-',
                    $row->name ?? '-',
                    $row->user ? $row->user->username : '-',
                    $row->user ? $row->user->email : '-',
                    $row->phone_number ?? '-',
                    $row->tahun_lulus ?? '-',
                    $row->kdpstmsmh ?? '-',
                    $row->status ?? '-'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Remove alumni profile AND its associated user account safely.
     * DELETE /api/admin/alumni/{id} (Targetnya adalah ID ALUMNI, bukan ID USER)
     */
    public function destroy($id): JsonResponse
    {
        $alumni = Alumni::find($id);

        if (!$alumni) {
            return response()->json([
                'success' => false,
                'message' => 'Data alumni tidak ditemukan.'
            ], 404);
        }

        DB::beginTransaction();
        try {
            $user = $alumni->user;

            // Hapus profil alumni
            $alumni->delete();

            // Hapus akun user yang terhubung
            if ($user) {
                $user->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data alumni dan akun pengguna berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }
}
