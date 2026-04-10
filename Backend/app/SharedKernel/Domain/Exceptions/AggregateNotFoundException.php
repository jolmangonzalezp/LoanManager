<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\Exceptions;

class AggregateNotFoundException extends DomainException
{
    public function __construct(string $aggregateType, string $identifier)
    {
        $message = "No se encontró {$aggregateType} con el identificador proporcionado";
        parent::__construct($message);
    }
}
