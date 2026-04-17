<?php

declare(strict_types=1);

namespace App\ReportBC\Application\DTO;

final class LoanDelinquencyDTO
{
    public function __construct(
        public readonly string $loanId,
        public readonly string $loanNumber,
        public readonly string $customerName,
        public readonly int $saldoPendiente,
        public readonly int $diasAtraso,
        public readonly string $estado
    ) {}

    public function toArray(): array
    {
        return [
            'loan_id' => $this->loanId,
            'loan_number' => $this->loanNumber,
            'customer_name' => $this->customerName,
            'saldo_pendiente' => $this->saldoPendiente,
            'dias_atraso' => $this->diasAtraso,
            'estado' => $this->estado,
        ];
    }
}
