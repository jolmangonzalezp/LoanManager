<?php

declare(strict_types=1);

namespace App\SharedKernel\Application\Exceptions;

class UnauthorizedException extends ApplicationException
{
    public function __construct(string $message = 'No autenticado')
    {
        parent::__construct($message);
    }
}
