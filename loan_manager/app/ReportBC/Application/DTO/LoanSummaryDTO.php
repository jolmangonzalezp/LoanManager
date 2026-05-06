<?php

declare(strict_types=1);

namespace App\ReportBC\Application\DTO;

final class LoanSummaryDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $loanNumber,
        public readonly string $customerId,
        public readonly string $customerName,
        public readonly int $capitalOriginal,
        public readonly int $saldoPendiente,
        public readonly float $tasaInteres,
        public readonly string $fechaDesembolso,
        public readonly string $proximoPago,
        public readonly string $estado,
        public readonly int $diasActivo
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'loan_number' => $this->loanNumber,
            'customer_id' => $this->customerId,
            'customer_name' => $this->customerName,
            'capital_original' => $this->capitalOriginal,
            'saldo_pendiente' => $this->saldoPendiente,
            'tasa_interes' => $this->tasaInteres,
            'fecha_desembolso' => $this->fechaDesembolso,
            'proximo_pago' => $this->proximoPago,
            'estado' => $this->estado,
            'dias_activo' => $this->diasActivo,
        ];
    }
}
