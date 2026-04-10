<?php

declare(strict_types=1);

namespace App\SharedKernel\Application\Exceptions;

class ServiceUnavailableException extends ApplicationException
{
    public function __construct(string $message = 'Servicio temporalmente no disponible')
    {
        parent::__construct($message);
    }
}
