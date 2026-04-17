<?php

declare(strict_types=1);

namespace App\ReportBC\Application\DTO;

final class CollectionAvailabilityResponse
{
    public function __construct(
        public readonly string $loanId,
        public readonly string $customerId,
        public readonly string $customerName,
        public readonly string $interestDueDate,
        public readonly string $currentDate,
        public readonly int $pendingInterest,
        public readonly string $status
    ) {}

    public function toArray(): array
    {
        return [
            'loan_id' => $this->loanId,
            'customer_id' => $this->customerId,
            'customer_name' => $this->customerName,
            'interest_due_date' => $this->interestDueDate,
            'current_date' => $this->currentDate,
            'pending_interest' => $this->pendingInterest,
            'status' => $this->status,
        ];
    }
}
