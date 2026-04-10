<?php

declare(strict_types=1);

namespace App\PaymentBC\Infrastructure\Repositories;

use App\LoanBC\Domain\ValueObjects\LoanIdVO;
use App\PaymentBC\Domain\Repositories\PaymentFinderByLoanId;
use App\PaymentBC\Infrastructure\Models\PaymentModel;

final class EloquentPaymentFinderByLoanId implements PaymentFinderByLoanId
{
    public function findByLoanId(LoanIdVO $loanId): array
    {
        $mapper = new PaymentMapper;
        $models = PaymentModel::where('loan_id', $loanId->getValue())
            ->where('status', 'applied')
            ->orderBy('created_at', 'asc')
            ->get();

        return $models->map(fn ($m) => $mapper->toDomain($m))->all();
    }
}
