<?php

declare(strict_types=1);

namespace App\UserBC\Infrastructure\Persistence\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

final class UserModel extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        'remember_token',
        'email_verified_at',
        'enabled',
        'created_at',
        'updated_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'enabled' => 'boolean',
    ];

    public $incrementing = false;

    protected $keyType = 'string';
}
