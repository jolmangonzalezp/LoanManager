<?php

declare(strict_types=1);

namespace App\PaymentBC\Infrastructure\Persistence\Model;

use App\LoanBC\Infrastructure\Persistence\Model\LoanModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function loan(): BelongsTo
    {
        return $this->belongsTo(LoanModel::class, 'loan_id');
    }
}
