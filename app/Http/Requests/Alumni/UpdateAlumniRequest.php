<?php

namespace App\Http\Requests\Alumni;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateAlumniRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // return Auth::check();
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $alumniId = Auth::user()->alumni->id;
        return [
            'nim' => 'sometimes|string|unique:alumnis,nim,' . $alumniId,
            'nik' => 'sometimes|string|unique:alumnis,nik,' . $alumniId,
            'npwp' => 'sometimes|string|unique:alumnis,npwp,' . $alumniId,

            'email' => 'sometimes|email|unique:users,email,' . Auth::id(),

            'name' => 'sometimes|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'tahun_lulus' => 'nullable|digits:4|integer',
            'kdpstmsmh' => 'nullable|string|max:10',
            'privacy_settings' => 'nullable|array',
            'img_profile' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'nullable|string',
        ];
    }
}
