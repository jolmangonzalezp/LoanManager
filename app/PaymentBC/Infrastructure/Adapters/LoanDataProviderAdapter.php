<?php

declare(strict_types=1);

namespace App\PaymentBC\Infrastructure\Adapters;

use App\LoanBC\Infrastructure\Persistence\Model\LoanModel;
use App\PaymentBC\Domain\Ports\LoanDataProvider;

final readonly class LoanDataProviderAdapter implements LoanDataProvider
{
    public function getLoanNumber(string $loanId): ?string
    {
        $model = LoanModel::find($loanId);

        return $model?->loan_number;
    }

    public function getRemainingDebt(string $loanId): ?int
    {
        $model = LoanModel::find($loanId);

        return $model?->remaining_debt;
    }
}
