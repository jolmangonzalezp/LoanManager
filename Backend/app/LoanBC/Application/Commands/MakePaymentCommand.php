<?php

declare(strict_types=1);

namespace App\LoanBC\Application\Commands;

use App\LoanBC\Domain\ValueObjects\LoanIdVO;
use App\SharedKernel\Domain\ValueObjects\MoneyVO;

final class MakePaymentCommand
{
    public function __construct(
        public readonly LoanIdVO $loanId,
        public readonly MoneyVO $amount
    ) {}
}
