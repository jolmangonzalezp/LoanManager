<?php

declare(strict_types=1);

namespace App\CustomerBC\Application\DTO;

final class CustomerReportResponse
{
    public function __construct(
        public readonly int $totalCustomers,
        public readonly int $totalCustomersWithLoan,
        public readonly int $totalCustomersWithoutLoan
    ) {}

    public function toArray(): array
    {
        return [
            'total_customers' => $this->totalCustomers,
            'total_customers_with_loan' => $this->totalCustomersWithLoan,
            'total_customers_without_loan' => $this->totalCustomersWithoutLoan,
        ];
    }
}
