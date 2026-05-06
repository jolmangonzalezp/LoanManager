<?php

declare(strict_types=1);

namespace App\ReportBC\Application\DTO;

final class ProfitabilityReportDTO
{
    public function __construct(
        public readonly int $totalIntereses,
        public readonly int $totalCapital,
        public readonly float $ratioInteresesCapital,
        public readonly float $roiGlobal,
        public readonly array $roiPorPrestamo,
        public readonly int $totalPrestamos
    ) {}

    public function toArray(): array
    {
        return [
            'total_intereses' => $this->totalIntereses,
            'total_capital' => $this->totalCapital,
            'ratio_intereses_capital' => round($this->ratioInteresesCapital, 4),
            'roi_global' => round($this->roiGlobal, 4),
            'roi_por_prestamo' => $this->roiPorPrestamo,
            'total_prestamos' => $this->totalPrestamos,
        ];
    }
}
