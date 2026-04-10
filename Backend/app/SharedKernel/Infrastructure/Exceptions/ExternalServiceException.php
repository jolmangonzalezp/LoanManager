<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\Exceptions;

class ExternalServiceException extends InfrastructureException
{
    public function __construct(string $service, string $operation)
    {
        $message = "Error al {$operation} en servicio externo: {$service}";
        parent::__construct($message);
    }
}
