<?php

namespace App\Http\Requests\Alumni\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Alumni;

class UpdateAlumniByAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === 'admin';
    }

    public function rules(): array
    {
        // Mengambil ID Alumni dari URL (/api/admin/alumni/{id})
        $alumniId = $this->route('id');
        $alumni = Alumni::find($alumniId);

        // Jika tidak ketemu, biarkan validasi lolos agar di-handle oleh 404 di Controller
        $userId = $alumni ? $alumni->user_id : null;

        return [
            // Data Akun (Tabel Users)
            'username' => 'sometimes|required|string|max:50|unique:users,username,' . $userId,
            'email'    => 'sometimes|required|email|max:255|unique:users,email,' . $userId,
            'password' => 'nullable|string|min:8',

            // Data Profil (Tabel Alumnis)
            'name'         => 'sometimes|required|string|max:255',
            'nim'          => 'sometimes|string|unique:alumnis,nim,' . $alumniId,
            'nik'          => 'sometimes|string|unique:alumnis,nik,' . $alumniId,
            'npwp'         => 'sometimes|string|unique:alumnis,npwp,' . $alumniId,
            'phone_number' => 'nullable|string|max:20',
            'tahun_lulus'  => 'nullable|digits:4|integer',
            'kdpstmsmh'    => 'nullable|string|max:10',
            'privacy_settings' => 'nullable|array',
            'status'       => 'nullable|string',
        ];
    }
}
