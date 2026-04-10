<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\Exceptions;

class CacheException extends InfrastructureException
{
    public function __construct(string $operation)
    {
        $message = "Error al {$operation} datos en caché";
        parent::__construct($message);
    }
}
