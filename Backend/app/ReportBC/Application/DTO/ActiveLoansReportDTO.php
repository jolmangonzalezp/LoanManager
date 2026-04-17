<?php

declare(strict_types=1);

namespace App\ReportBC\Application\DTO;

final class ActiveLoansReportDTO
{
    public function __construct(
        public readonly array $prestamos,
        public readonly int $total,
        public readonly int $totalSaldo
    ) {}

    public function toArray(): array
    {
        return [
            'prestamos' => $this->prestamos,
            'total' => $this->total,
            'total_saldo' => $this->totalSaldo,
        ];
    }
}
