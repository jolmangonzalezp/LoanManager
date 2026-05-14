<?php

declare(strict_types=1);

namespace App\LoanBC\Application\CQRS\Command;

use App\CustomerBC\Domain\ValueObject\CustomerIdVO;
use App\LoanBC\Domain\ValueObject\InterestRateVO;
use App\LoanBC\Domain\ValueObject\LoanTypeIdVO;
use App\SharedKernel\Domain\ValueObject\DateVO;
use App\SharedKernel\Domain\ValueObject\MoneyVO;

final class CreateLoanCommand
{
    public function __construct(
        public readonly CustomerIdVO $customerId,
        public readonly ?LoanTypeIdVO $loanTypeId,
        public readonly MoneyVO $capital,
        public readonly InterestRateVO $interestRate,
        public readonly DateVO $startDate,
        public readonly int $term,
        public readonly ?string $loanTypeName = null
    ) {}
}
