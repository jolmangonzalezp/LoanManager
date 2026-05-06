<?php

declare(strict_types=1);

namespace App\LoanBC\Domain\Ports;

interface CustomerLoanStatistics
{
    public function getTotalCustomersWithLoan(): int;

    public function getTotalCustomersWithoutLoan(int $totalCustomers): int;
}
