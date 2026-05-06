<?php

declare(strict_types=1);

namespace App\ReportBC\Application\DTO;

final class AuditReportDTO
{
    public function __construct(
        public readonly array $registros,
        public readonly int $totalRegistros
    ) {}

    public function toArray(): array
    {
        return [
            'registros' => $this->registros,
            'total_registros' => $this->totalRegistros,
        ];
    }
}
