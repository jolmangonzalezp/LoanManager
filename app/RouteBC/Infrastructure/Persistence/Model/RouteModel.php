<?php

namespace App\RouteBC\Infrastructure\Persistence\Model;

use App\UserBC\Infrastructure\Persistence\Model\UserModel;
use Illuminate\Database\Eloquent\Model;

class RouteModel extends Model
{
    protected $table = 'routes';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'zone_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function users()
    {
        return $this->belongsToMany(UserModel::class, 'route_user', 'route_id', 'user_id')
            ->withPivot('assigned_at');
    }

    public function zone()
    {
        return $this->belongsTo(ZoneModel::class, 'zone_id');
    }
}
