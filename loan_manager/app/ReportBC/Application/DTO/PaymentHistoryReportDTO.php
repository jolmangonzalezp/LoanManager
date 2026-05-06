<?php

declare(strict_types=1);

namespace App\ReportBC\Application\DTO;

final class PaymentHistoryReportDTO
{
    public function __construct(
        public readonly array $pagos,
        public readonly int $total,
        public readonly int $montoTotal,
        public readonly ?string $loanId,
        public readonly ?string $customerId
    ) {}

    public function toArray(): array
    {
        return [
            'pagos' => $this->pagos,
            'total' => $this->total,
            'monto_total' => $this->montoTotal,
            'loan_id' => $this->loanId,
            'customer_id' => $this->customerId,
        ];
    }
}
