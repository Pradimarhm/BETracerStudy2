<?php

namespace App\Http\Requests\Alumni\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ImportAlumniRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            // Memaksa file harus bertipe CSV atau TXT (maksimal 5MB)
            'file' => 'required|file|mimes:csv,txt|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'file.mimes' => 'File harus berformat .csv. Silakan simpan Excel Anda sebagai CSV (Comma delimited).',
        ];
    }
}
