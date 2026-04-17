<?php

declare(strict_types=1);

namespace App\ReportBC\Application\DTO;

final class AuditEntryDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $tipo,
        public readonly string $entidad,
        public readonly string $entidadId,
        public readonly ?int $usuarioId,
        public readonly ?string $usuarioNombre,
        public readonly string $accion,
        public readonly array $datosAnteriores,
        public readonly array $datosNuevos,
        public readonly string $fecha
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'tipo' => $this->tipo,
            'entidad' => $this->entidad,
            'entidad_id' => $this->entidadId,
            'usuario_id' => $this->usuarioId,
            'usuario_nombre' => $this->usuarioNombre,
            'accion' => $this->accion,
            'datos_anteriores' => $this->datosAnteriores,
            'datos_nuevos' => $this->datosNuevos,
            'fecha' => $this->fecha,
        ];
    }
}
