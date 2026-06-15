<?php

namespace App\Http\Requests\Job;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;


class StoreJobVacancyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\JobVacancy::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:100',
            'poster_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Max 2MB
            'expired_at' => 'nullable|date|after_or_equal:today',
            'is_active' => 'boolean',
        ];
    }
}
