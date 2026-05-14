<?php

declare(strict_types=1);

namespace App\LoanBC\Infrastructure\Persistence\Model;

use Illuminate\Database\Eloquent\Model;

final class LoanTypeModel extends Model
{
    protected $table = 'loan_types';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'description',
    ];

    public static function findByName(string $name): ?self
    {
        return static::where('name', $name)->first();
    }
}
