<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class News extends Model
{
    protected $fillable = ['user_id', 'title', 'slug', 'content', 'thumbnail', 'category', 'is_published'];

    // Otomatis buat slug saat title diisi (Opsional, bisa dilakukan di Controller)
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($news) {
            $news->slug = Str::slug($news->title);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
