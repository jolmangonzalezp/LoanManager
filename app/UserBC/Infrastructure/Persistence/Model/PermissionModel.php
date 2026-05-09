<?php

declare(strict_types=1);

namespace App\UserBC\Infrastructure\Persistence\Model;

use Illuminate\Database\Eloquent\Model;

final class PermissionModel extends Model
{
    protected $table = 'permissions';

    protected $fillable = [
        'id',
        'slug',
        'name',
        'description',
        'group',
        'created_at',
        'updated_at',
    ];

    public $incrementing = false;

    protected $keyType = 'string';
}
