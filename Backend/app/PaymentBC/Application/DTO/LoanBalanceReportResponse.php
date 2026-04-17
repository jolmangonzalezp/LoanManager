<?php

declare(strict_types=1);

namespace App\PaymentBC\Application\DTO;

final class LoanBalanceReportResponse
{
    public function __construct(
        public readonly string $loanId,
        public readonly string $loanNumber,
        public readonly string $customerName,
        public readonly int $originalCapital,
        public readonly int $projectedBalance,
        public readonly int $actualBalance,
        public readonly int $capitalPaid,
        public readonly int $interestPaid,
        public readonly int $difference,
        public readonly string $status
    ) {}

    public function toArray(): array
    {
        return [
            'loan_id' => $this->loanId,
            'loan_number' => $this->loanNumber,
            'customer_name' => $this->customerName,
            'original_capital' => $this->originalCapital,
            'projected_balance' => $this->projectedBalance,
            'actual_balance' => $this->actualBalance,
            'capital_paid' => $this->capitalPaid,
            'interest_paid' => $this->interestPaid,
            'difference' => $this->difference,
            'status' => $this->status,
        ];
    }
}
