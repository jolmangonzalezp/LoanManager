<?php

declare(strict_types=1);

namespace App\ReportBC\Application\DTOs;

final class ProjectedVsActualResponse
{
    public function __construct(
        public readonly string $loanId,
        public readonly string $customerId,
        public readonly string $customerName,
        public readonly int $previousBalance,
        public readonly int $monthlyInterest,
        public readonly int $totalPayment,
        public readonly int $newBalance,
        public readonly int $interestPaid,
        public readonly int $capitalPaid,
        public readonly int $difference
    ) {}

    public function toArray(): array
    {
        return [
            'loan_id' => $this->loanId,
            'customer_id' => $this->customerId,
            'customer_name' => $this->customerName,
            'previous_balance' => $this->previousBalance,
            'monthly_interest' => $this->monthlyInterest,
            'total_payment' => $this->totalPayment,
            'new_balance' => $this->newBalance,
            'interest_paid' => $this->interestPaid,
            'capital_paid' => $this->capitalPaid,
            'capital_reduction' => $this->capitalPaid,
            'difference' => $this->difference,
        ];
    }
}
