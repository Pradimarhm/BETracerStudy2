<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $questionnaire_id
 * @property int|null $parent_id
 * @property string $kode
 * @property string $text
 * @property string $type
 * @property int $order
 * @property bool $is_required
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Question> $children
 * @property-read int|null $children_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\QuestionOption> $options
 * @property-read int|null $options_count
 * @property-read Question|null $parent
 * @property-read \App\Models\Questionnaire $questionnaire
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereIsRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereKode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereQuestionnaireId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Question extends Model
{
    protected $fillable = ['questionnaire_id', 'parent_id', 'kode', 'text', 'type', 'order', 'is_required'];

public function questionnaire() {
    return $this->belongsTo(Questionnaire::class);
}

// Relasi untuk pertanyaan bercabang
public function children() {
    return $this->hasMany(Question::class, 'parent_id');
}

public function parent() {
    return $this->belongsTo(Question::class, 'parent_id');
}

public function options()
{
    return $this->hasMany(QuestionOption::class, 'question_id')->orderBy('order', 'asc');
}
}
