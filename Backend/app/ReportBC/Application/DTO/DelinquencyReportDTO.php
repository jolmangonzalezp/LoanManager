<?php

declare(strict_types=1);

namespace App\ReportBC\Application\DTO;

final class DelinquencyReportDTO
{
    public function __construct(
        public readonly int $clientesEnMora,
        public readonly int $montoEnMora,
        public readonly int $diasPromedioAtraso,
        public readonly float $porcentajeCarteraVencida,
        public readonly int $prestamosEnMora,
        public readonly array $detalleMora
    ) {}

    public function toArray(): array
    {
        return [
            'clientes_en_mora' => $this->clientesEnMora,
            'monto_en_mora' => $this->montoEnMora,
            'dias_promedio_atraso' => $this->diasPromedioAtraso,
            'porcentaje_cartera_vencida' => round($this->porcentajeCarteraVencida, 2),
            'prestamos_en_mora' => $this->prestamosEnMora,
            'detalle_mora' => $this->detalleMora,
        ];
    }
}
