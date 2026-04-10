<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\Exceptions;

class EntityNotFoundException extends DomainException
{
    public function __construct(string $entityType, string $identifier)
    {
        $message = "No se encontró {$entityType} con el identificador proporcionado";
        parent::__construct($message);
    }
}
