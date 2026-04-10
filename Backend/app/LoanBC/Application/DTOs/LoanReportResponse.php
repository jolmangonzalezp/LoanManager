<?php

declare(strict_types=1);

namespace App\LoanBC\Application\DTOs;

final class LoanReportResponse
{
    public function __construct(
        public readonly int $totalLoans,
        public readonly int $activeLoans,
        public readonly int $paidLoans,
        public readonly int $defaultedLoans,
        public readonly int $totalCapital,
        public readonly int $totalRemainingDebt,
        public readonly int $totalPaidCapital,
        public readonly int $totalPaidInterest
    ) {}

    public function toArray(): array
    {
        return [
            'total_loans' => $this->totalLoans,
            'active_loans' => $this->activeLoans,
            'paid_loans' => $this->paidLoans,
            'defaulted_loans' => $this->defaultedLoans,
            'total_capital' => $this->totalCapital,
            'total_remaining_debt' => $this->totalRemainingDebt,
            'total_paid_capital' => $this->totalPaidCapital,
            'total_paid_interest' => $this->totalPaidInterest,
        ];
    }
}
