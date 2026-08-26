<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $title
 * @property int $year
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Question> $questions
 * @property-read int|null $questions_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Questionnaire newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Questionnaire newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Questionnaire query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Questionnaire whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Questionnaire whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Questionnaire whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Questionnaire whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Questionnaire whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Questionnaire whereYear($value)
 * @mixin \Eloquent
 */
class Questionnaire extends Model
{
    // use HasFactory;

    protected $fillable = [
        'title',
        'year',
        'is_active',
    ];

    /**
     * Relasi ke daftar pertanyaan (One-to-Many).
     */
    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
