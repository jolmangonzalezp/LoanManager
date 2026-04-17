<?php

declare(strict_types=1);

namespace App\ReportBC\Application\DTO;

final class LoanProfitabilityDTO
{
    public function __construct(
        public readonly string $loanId,
        public readonly string $loanNumber,
        public readonly string $customerName,
        public readonly int $capital,
        public readonly int $interesesCobrados,
        public readonly int $diasActivo,
        public readonly float $roi
    ) {}

    public function toArray(): array
    {
        return [
            'loan_id' => $this->loanId,
            'loan_number' => $this->loanNumber,
            'customer_name' => $this->customerName,
            'capital' => $this->capital,
            'intereses_cobrados' => $this->interesesCobrados,
            'dias_activo' => $this->diasActivo,
            'roi' => round($this->roi, 4),
        ];
    }
}
