<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class JobVacancyResource extends JsonResource
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
            'company' => $this->company,
            'description' => $this->description,
            'location' => $this->location,
            'category' => $this->category,
            'poster_image' => $this->poster_image ? Storage::url($this->poster_image) : null,
            'is_active' => (bool) $this->is_active,
            // 'expired_at' => $this->expired_at ? $this->expired_at->format('Y-m-d') : null,
            'expired_at' => $this->expired_at ? \Illuminate\Support\Carbon::parse($this->expired_at)->format('Y-m-d') : null,
            'posted_by' => [
                'id' => $this->user->id,
                'name' => $this->user->role === 'alumni' ? $this->user->alumni->name : 'Admin Official',
            ],
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
