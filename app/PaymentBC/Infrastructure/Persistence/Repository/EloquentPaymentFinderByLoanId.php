<?php

declare(strict_types=1);

namespace App\PaymentBC\Infrastructure\Persistence\Repository;

use App\LoanBC\Domain\ValueObject\LoanIdVO;
use App\PaymentBC\Domain\Repository\PaymentFinderByLoanId;
use App\PaymentBC\Infrastructure\Mapper\PaymentMapper;
use App\PaymentBC\Infrastructure\Persistence\Model\PaymentModel;

final class EloquentPaymentFinderByLoanId implements PaymentFinderByLoanId
{
    public function __construct(
        private readonly PaymentMapper $mapper
    ) {}

    public function findByLoanId(LoanIdVO $loanId): array
    {
        $models = PaymentModel::where('loan_id', $loanId->getValue())
            ->where('status', 'applied')
            ->orderBy('created_at', 'asc')
            ->get();

        return $models->map(fn ($m) => $this->mapper->toDomain($m))->all();
    }
}
