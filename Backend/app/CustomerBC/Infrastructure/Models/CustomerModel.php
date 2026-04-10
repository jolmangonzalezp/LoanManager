<?php

namespace App\CustomerBC\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerModel extends Model
{
    protected $table = 'customers';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'first_name',
        'last_name',
        'second_last_name',
        'middle_name',
        'dni_number',
        'dni_hash',
        'dni_type',
        'phone_number',
        'phone_country_code',
        'address',
        'email',
        'enabled',
        'created_at',
    ];

    protected $hidden = [
        'first_name',
        'last_name',
        'second_last_name',
        'middle_name',
        'dni_number',
        'phone_number',
        'address',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'enabled' => 'boolean',
    ];
}
