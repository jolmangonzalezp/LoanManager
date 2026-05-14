<?php

namespace App\RouteBC\Infrastructure\Persistence\Model;

use Illuminate\Database\Eloquent\Model;

class ZoneModel extends Model
{
    protected $table = 'zones';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'polygon',
    ];

    protected $casts = [
        'polygon' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
