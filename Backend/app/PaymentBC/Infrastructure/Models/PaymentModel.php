<?php

declare(strict_types=1);

namespace App\PaymentBC\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;

final class PaymentModel extends Model
{
    protected $table = 'payments';

    protected $fillable = [
        'id',
        'loan_id',
        'amount',
        'payment_date',
        'interest_paid',
        'capital_paid',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'amount' => 'integer',
        'payment_date' => 'datetime',
        'interest_paid' => 'integer',
        'capital_paid' => 'integer',
    ];

    public $incrementing = false;

    protected $keyType = 'string';
}
