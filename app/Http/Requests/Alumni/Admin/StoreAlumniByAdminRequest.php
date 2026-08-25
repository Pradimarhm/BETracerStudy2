<?php

namespace App\Http\Requests\Alumni\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreAlumniByAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Pastikan hanya admin yang bisa mengeksekusi ini
        return $this->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'name'     => 'required|string|max:255',
            'username' => 'required|string|unique:users,username|max:50',
            'email'    => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:8',
        ];
    }
}
