<?php

declare(strict_types=1);

namespace App\PaymentBC\Domain\Repositories;

use App\LoanBC\Domain\ValueObjects\LoanIdVO;

interface PaymentFinderByLoanId
{
    public function findByLoanId(LoanIdVO $loanId): array;
}
