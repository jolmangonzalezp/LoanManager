<?php

declare(strict_types=1);

namespace App\LoanBC\Application\Commands;

use App\CustomerBC\Domain\ValueObjects\CustomerIdVO;
use App\LoanBC\Domain\ValueObjects\InterestRateVO;
use App\SharedKernel\Domain\ValueObjects\DateVO;
use App\SharedKernel\Domain\ValueObjects\MoneyVO;

final class CreateLoanCommand
{
    public function __construct(
        public readonly CustomerIdVO $customerId,
        public readonly MoneyVO $capital,
        public readonly InterestRateVO $interestRate,
        public readonly DateVO $startDate,
        public readonly DateVO $dueDate
    ) {}
}
