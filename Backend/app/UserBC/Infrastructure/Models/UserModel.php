<?php

declare(strict_types=1);

namespace App\UserBC\Infrastructure\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

final class UserModel extends Authenticatable
{
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
