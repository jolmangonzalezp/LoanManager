<?php

declare(strict_types=1);

namespace App\UserBC\Infrastructure\Persistence\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class RoleModel extends Model
{
    protected $table = 'roles';

    protected $fillable = [
        'id',
        'slug',
        'name',
        'description',
        'is_system',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'is_system' => 'boolean',
    ];

    public $incrementing = false;

    protected $keyType = 'string';

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(PermissionModel::class, 'role_permission', 'role_id', 'permission_id');
    }
}
