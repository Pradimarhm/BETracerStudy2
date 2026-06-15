<?php

namespace App\Http\Requests\Job;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateJobVacancyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // return $this->user()->can('update', $this->route('job_vacancy'));
        // 1. Ambil ID dari rute URL (/api/jobs/{id})
        $jobId = $this->route('id');

        // 2. Ambil data modelnya dari database berdasarkan ID tersebut
        $jobVacancy = \App\Models\JobVacancy::find($jobId);

        // 3. Jika data tidak ada di DB, tolak langsung (403/404)
        if (!$jobVacancy) {
            return false;
        }

        // 4. Kirim objek model utuh ke Policy untuk dicek hak aksesnya
        return $this->user()->can('update', $jobVacancy);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'sometimes|string|max:255',
            'company' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'location' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:100',
            'poster_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'expired_at' => 'nullable|date|after_or_equal:today',
            'is_active' => 'boolean',
        ];
    }
}
