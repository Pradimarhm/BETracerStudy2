<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class NewsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'category' => $this->category,
            'thumbnail' => $this->thumbnail ? Storage::url($this->thumbnail) : null,
            'is_published' => (bool) $this->is_published,
            'author' => [
                'id' => $this->user->id,
                'name' => $this->user->role === 'admin' ? 'Administrator' : ($this->user->alumni->name ?? $this->user->name),
            ],
            'created_at' => $this->created_at->translatedFormat('d F Y'), // Contoh: 09 Mei 2026
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
