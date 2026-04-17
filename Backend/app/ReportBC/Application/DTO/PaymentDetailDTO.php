<?php

declare(strict_types=1);

namespace App\ReportBC\Application\DTO;

final class PaymentDetailDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $loanId,
        public readonly string $loanNumber,
        public readonly ?string $customerId,
        public readonly string $customerName,
        public readonly int $monto,
        public readonly int $interesPagado,
        public readonly int $capitalPagado,
        public readonly string $fechaPago,
        public readonly string $estado,
        public readonly ?int $diasAtraso
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'loan_id' => $this->loanId,
            'loan_number' => $this->loanNumber,
            'customer_id' => $this->customerId,
            'customer_name' => $this->customerName,
            'monto' => $this->monto,
            'interes_pagado' => $this->interesPagado,
            'capital_pagado' => $this->capitalPagado,
            'fecha_pago' => $this->fechaPago,
            'estado' => $this->estado,
            'dias_atraso' => $this->diasAtraso,
        ];
    }
}
