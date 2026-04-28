<?php

declare(strict_types=1);

namespace App\LoanBC\Infrastructure\Persistence\Model;

use App\CustomerBC\Infrastructure\Persistence\Model\CustomerModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class LoanModel extends Model
{
    protected $table = 'loans';

    protected $fillable = [
        'id',
        'loan_number',
        'customer_id',
        'original_capital',
        'remaining_debt',
        'paid_capital',
        'paid_interest',
        'pending_interest',
        'interest_period',
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
        'remaining_debt' => 'integer',
        'paid_capital' => 'integer',
        'paid_interest' => 'integer',
        'pending_interest' => 'integer',
        'interest_rate' => 'float',
        'next_payment_date' => 'datetime',
        'start_date' => 'datetime',
        'due_date' => 'datetime',
    ];

    public $incrementing = false;

    protected $keyType = 'string';

    public function customer(): BelongsTo
    {
        return $this->belongsTo(CustomerModel::class, 'customer_id');
    }
}