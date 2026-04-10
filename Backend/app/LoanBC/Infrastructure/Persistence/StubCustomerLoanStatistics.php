<?php

declare(strict_types=1);

namespace App\LoanBC\Infrastructure\Persistence;

use App\LoanBC\Domain\Ports\CustomerLoanStatistics;

final class StubCustomerLoanStatistics implements CustomerLoanStatistics
{
    public function getTotalCustomersWithLoan(): int
    {
        return 0;
    }

    public function getTotalCustomersWithoutLoan(int $totalCustomers): int
    {
        return $totalCustomers;
    }
}
