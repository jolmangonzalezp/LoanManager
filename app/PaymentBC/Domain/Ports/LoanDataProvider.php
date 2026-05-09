<?php

declare(strict_types=1);

namespace App\PaymentBC\Domain\Ports;

interface LoanDataProvider
{
    public function getLoanNumber(string $loanId): ?string;

    public function getRemainingDebt(string $loanId): ?int;
}
