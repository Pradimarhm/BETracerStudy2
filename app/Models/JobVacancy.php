<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string $company
 * @property string $description
 * @property string|null $location
 * @property string|null $poster_image
 * @property string|null $category
 * @property bool $is_active
 * @property string|null $expired_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobVacancy newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobVacancy newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobVacancy query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobVacancy whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobVacancy whereCompany($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobVacancy whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobVacancy whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobVacancy whereExpiredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobVacancy whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobVacancy whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobVacancy whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobVacancy wherePosterImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobVacancy whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobVacancy whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobVacancy whereUserId($value)
 * @mixin \Eloquent
 */
class JobVacancy extends Model
{
    protected $fillable = [
    'user_id', 'title', 'company', 'description', 
    'location', 'poster_image', 'category', 'is_active', 'expired_at'
];

/**
 * Relasi balik ke User (Bisa Admin atau Alumni)
 */
public function user()
{
    return $this->belongsTo(User::class);
}
}
