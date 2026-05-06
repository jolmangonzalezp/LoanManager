<?php

declare(strict_types=1);

namespace App\ReportBC\Application\DTO;

final class KpiReportDTO
{
    public function __construct(
        public readonly float $tasaMora,
        public readonly float $tasaRecuperacion,
        public readonly int $ticketPromedio,
        public readonly float $duracionPromedioPrestamo,
        public readonly float $porcentajeClientesRecurrentes,
        public readonly int $totalPrestamos,
        public readonly int $totalClientes,
        public readonly int $totalPrestamosCerrados
    ) {}

    public function toArray(): array
    {
        return [
            'tasa_mora' => round($this->tasaMora, 2),
            'tasa_recuperacion' => round($this->tasaRecuperacion, 2),
            'ticket_promedio' => $this->ticketPromedio,
            'duracion_promedio_prestamo' => round($this->duracionPromedioPrestamo, 1),
            'porcentaje_clientes_recurrentes' => round($this->porcentajeClientesRecurrentes, 2),
            'total_prestamos' => $this->totalPrestamos,
            'total_clientes' => $this->totalClientes,
            'total_prestamos_cerrados' => $this->totalPrestamosCerrados,
        ];
    }
}
