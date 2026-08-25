<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class AlumniResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Logika sederhana: Jika privacy show_phone false, sembunyikan nomor HP
        $privacy = $this->privacy_settings;
        $showPhone = $privacy['show_phone'] ?? true;

        return [
            'id' => $this->id,
            'user_id' => $this->user->id ?? null,
            'nim' => $this->nim,
            'name' => $this->name, // Mengambil data terbaru dari tabel users
            'email' => $this->user->email,   // Mengambil data terbaru dari tabel users
            'nik' => $this->nik,
            'npwp' => $this->npwp,
            'phone_number' => $showPhone ? $this->phone_number : 'Private',
            'img_profile' => $this->img_profile ? Storage::url($this->img_profile) : null,
            'privacy_settings' => $this->privacy_settings ?? 'public',
            'tahun_lulus' => $this->tahun_lulus,
            'kdpstmsmh' => $this->kdpstmsmh,
            'status' => $this->status,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
