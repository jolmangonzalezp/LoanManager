<?php

declare(strict_types=1);

namespace App\ReportBC\Application\DTO;

final class PortfolioReportDTO
{
    public function __construct(
        public readonly int $totalPrestado,
        public readonly int $capitalPendiente,
        public readonly int $interesesGenerados,
        public readonly int $interesesCobrados,
        public readonly int $numeroPrestamosActivos,
        public readonly float $tasaInteresPromedio,
        public readonly int $totalClientes
    ) {}

    public function toArray(): array
    {
        return [
            'total_prestado' => $this->totalPrestado,
            'capital_pendiente' => $this->capitalPendiente,
            'intereses_generados' => $this->interesesGenerados,
            'intereses_cobrados' => $this->interesesCobrados,
            'numero_prestamos_activos' => $this->numeroPrestamosActivos,
            'tasa_interes_promedio' => round($this->tasaInteresPromedio, 2),
            'total_clientes' => $this->totalClientes,
        ];
    }
}
