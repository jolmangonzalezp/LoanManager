<?php

declare(strict_types=1);

namespace App\ReportBC\Application\DTO;

final class CashFlowReportDTO
{
    public function __construct(
        public readonly int $ingresosPorPagos,
        public readonly int $egresosPorDesembolsos,
        public readonly int $flujoNeto,
        public readonly string $fechaInicio,
        public readonly string $fechaFin,
        public readonly int $totalPagos,
        public readonly int $totalDesembolsos
    ) {}

    public function toArray(): array
    {
        return [
            'ingresos_por_pagos' => $this->ingresosPorPagos,
            'egresos_por_desembolsos' => $this->egresosPorDesembolsos,
            'flujo_neto' => $this->flujoNeto,
            'fecha_inicio' => $this->fechaInicio,
            'fecha_fin' => $this->fechaFin,
            'total_pagos' => $this->totalPagos,
            'total_desembolsos' => $this->totalDesembolsos,
        ];
    }
}
