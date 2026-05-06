<?php

declare(strict_types=1);

namespace App\PaymentBC\Application\DTO;

final class MonthlyReportResponse
{
    public function __construct(
        public readonly int $capitalReturned,
        public readonly int $interestCollected,
        public readonly int $paymentsCount,
        public readonly string $month,
        public readonly string $year
    ) {}

    public function toArray(): array
    {
        return [
            'capital_returned' => $this->capitalReturned,
            'interest_collected' => $this->interestCollected,
            'payments_count' => $this->paymentsCount,
            'month' => $this->month,
            'year' => $this->year,
        ];
    }
}
