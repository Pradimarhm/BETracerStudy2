<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $question_id
 * @property int $alumni_id
 * @property int|null $question_option_id
 * @property string|null $answer_text
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Alumni $alumni
 * @property-read \App\Models\Question $question
 * @property-read \App\Models\QuestionOption|null $questionOption
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Answer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Answer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Answer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Answer whereAlumniId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Answer whereAnswerText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Answer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Answer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Answer whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Answer whereQuestionOptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Answer whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Answer extends Model
{
    protected $fillable = ['question_id', 'alumni_id', 'question_option_id', 'answer_text'];

    public function alumni() { 
        return $this->belongsTo(Alumni::class); 
        }

    public function question()
{
    return $this->belongsTo(Question::class);
}

public function questionOption()
{
    return $this->belongsTo(QuestionOption::class, 'question_option_id');
}
}


