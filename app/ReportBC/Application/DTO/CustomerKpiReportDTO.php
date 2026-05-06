<?php

declare(strict_types=1);

namespace App\ReportBC\Application\DTO;

final class CustomerKpiReportDTO
{
    public function __construct(
        public readonly int $totalCustomers,
        public readonly int $customersWithActiveLoans,
        public readonly int $customersWithLoans,
        public readonly int $customersWithoutLoans,
        public readonly int $activeCustomers
    ) {}

    public function toArray(): array
    {
        return [
            'total_customers' => $this->totalCustomers,
            'customers_with_active_loans' => $this->customersWithActiveLoans,
            'customers_with_loans' => $this->customersWithLoans,
            'customers_without_loans' => $this->customersWithoutLoans,
            'active_customers' => $this->activeCustomers,
        ];
    }
}