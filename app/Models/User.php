<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $primaryKey = 'user_id';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'username',
        'password_hash',
        'email',
        'full_name',
        'family_card_number',
        'phone_number',
        'address',
        'blok_rumah',
        'rt',
        'role_id',
        'profile_photo',
        'household_name',
        'position',
    ];

    protected $hidden = [
        'password_hash',
    ];

    public function getAuthPasswordName(): string
    {
        return 'password_hash';
    }

    protected function casts(): array
    {
        return [
            'password_hash' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        // Relasi ke tabel roles
        return $this->belongsTo(
            Role::class,
            'role_id',
            'role_id'
        );
    }

    public function accounts(): HasMany
    {
        // Relasi ke tabel accounts
        return $this->hasMany(
            Account::class,
            'user_id',
            'user_id'
        );
    }
}
