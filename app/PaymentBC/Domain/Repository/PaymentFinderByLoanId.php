<?php

declare(strict_types=1);

namespace App\PaymentBC\Domain\Repository;

use App\PaymentBC\Domain\ValueObject\LoanIdVO;

interface PaymentFinderByLoanId
{
    public function findByLoanId(LoanIdVO $loanId): array;
}
