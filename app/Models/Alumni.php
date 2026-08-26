<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property string|null $nim
 * @property string|null $nik
 * @property string|null $npwp
 * @property string $name
 * @property string|null $phone_number
 * @property string|null $img_profile
 * @property array<array-key, mixed>|null $privacy_settings
 * @property int|null $tahun_lulus
 * @property string|null $kdpstmsmh
 * @property string|null $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alumni newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alumni newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alumni query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alumni whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alumni whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alumni whereImgProfile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alumni whereKdpstmsmh($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alumni whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alumni whereNik($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alumni whereNim($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alumni whereNpwp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alumni wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alumni wherePrivacySettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alumni whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alumni whereTahunLulus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alumni whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alumni whereUserId($value)
 * @mixin \Eloquent
 */
class Alumni extends Model
{
    protected $fillable = [
    'user_id', 'nim', 'nik', 'npwp', 'name', 'phone_number', 
    'img_profile', 'privacy_settings', 'tahun_lulus', 'kdpstmsmh', 'status'
];

protected $casts = [
    'privacy_settings' => 'array', // Casting JSON ke Array
];

public function user()
{
    return $this->belongsTo(User::class);
}
}
