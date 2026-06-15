<?php

namespace App\Http\Requests\News;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateNewsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // return $this->user()->can('update', $this->route('news'));
        // 1. Ambil ID dari rute {id}
        $newsId = $this->route('id');

        // 2. Cari data beritanya dari database
        $news = \App\Models\News::find($newsId);

        // 3. Masukkan ke Policy
        return $news && $this->user()->can('update', $news);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // $newsId = $this->route('news')->id;
        $newsId = $this->route('id');

        return [
            'title' => 'sometimes|string|max:255|unique:news,title,' . $newsId,
            'content' => 'sometimes|string',
            'category' => 'nullable|string|max:100',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_published' => 'boolean',
        ];
    }
}
