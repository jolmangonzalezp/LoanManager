<?php

declare(strict_types=1);

namespace App\ReportBC\Application\DTO;

final class ClientProfitabilityResponse
{
    public function __construct(
        public readonly string $customerId,
        public readonly string $customerName,
        public readonly int $initialCapital,
        public readonly int $totalInterestPaid,
        public readonly int $totalCapitalPaid,
        public readonly int $remainingBalance,
        public readonly float $roiPercentage
    ) {}

    public function toArray(): array
    {
        return [
            'customer_id' => $this->customerId,
            'customer_name' => $this->customerName,
            'initial_capital' => $this->initialCapital,
            'total_interest_paid' => $this->totalInterestPaid,
            'total_capital_paid' => $this->totalCapitalPaid,
            'remaining_balance' => $this->remainingBalance,
            'roi_percentage' => $this->roiPercentage,
        ];
    }
}
