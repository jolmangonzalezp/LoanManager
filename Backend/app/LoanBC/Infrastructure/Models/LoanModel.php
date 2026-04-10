<?php

declare(strict_types=1);

namespace App\LoanBC\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;

final class LoanModel extends Model
{
    protected $table = 'loans';

    protected $fillable = [
        'id',
        'customer_id',
        'original_capital',
        'capital',
        'remaining_debt',
        'paid_capital',
        'paid_interest',
        'interest_rate',
        'start_date',
        'due_date',
        'next_payment_date',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'original_capital' => 'integer',
        'capital' => 'integer',
        'remaining_debt' => 'integer',
        'paid_capital' => 'integer',
        'paid_interest' => 'integer',
        'interest_rate' => 'float',
        'next_payment_date' => 'datetime',
        'start_date' => 'datetime',
        'due_date' => 'datetime',
    ];

    public $incrementing = false;

    protected $keyType = 'string';
}
