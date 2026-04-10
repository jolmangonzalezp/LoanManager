<?php

declare(strict_types=1);

namespace App\SharedKernel\Application\Exceptions;

class ForbiddenException extends ApplicationException
{
    public function __construct(string $message = 'Sin permisos para realizar esta acción')
    {
        parent::__construct($message);
    }
}
