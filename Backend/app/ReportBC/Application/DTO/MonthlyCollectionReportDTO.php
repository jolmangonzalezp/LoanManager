<?php

declare(strict_types=1);

namespace App\ReportBC\Application\DTO;

final class MonthlyCollectionReportDTO
{
    public function __construct(
        public readonly int $montoEsperado,
        public readonly int $montoCobrado,
        public readonly float $porcentajeCumplimiento,
        public readonly int $numeroPagos,
        public readonly int $numeroCuotasVencidas,
        public readonly string $mes,
        public readonly string $anio
    ) {}

    public function toArray(): array
    {
        return [
            'monto_esperado' => $this->montoEsperado,
            'monto_cobrado' => $this->montoCobrado,
            'porcentaje_cumplimiento' => round($this->porcentajeCumplimiento, 2),
            'numero_pagos' => $this->numeroPagos,
            'numero_cuotas_vencidas' => $this->numeroCuotasVencidas,
            'mes' => $this->mes,
            'anio' => $this->anio,
        ];
    }
}
