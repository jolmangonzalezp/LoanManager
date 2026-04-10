<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\Exceptions;

class InvalidUuidException extends DomainException
{
    public function __construct(string $uuid)
    {
        $message = "El identificador no es válido: {$uuid}";
        parent::__construct($message);
    }
}
