<?php

declare(strict_types=1);

namespace App\LoanBC\Domain\DTO;

use App\LoanBC\Domain\Aggregate\Loan;
use App\SharedKernel\Domain\ValueObject\MoneyVO;

final class PaymentDistributionResult
{
    public function __construct(
        public readonly Loan $loan,
        public readonly MoneyVO $interestPortion,
        public readonly MoneyVO $capitalPortion
    ) {}
}