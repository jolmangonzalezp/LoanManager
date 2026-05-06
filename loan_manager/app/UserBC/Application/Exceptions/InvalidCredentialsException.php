<?php

declare(strict_types=1);

namespace App\UserBC\Application\Exceptions;

use App\SharedKernel\Domain\Exception\DomainException;

class InvalidCredentialsException extends DomainException
{
    public function __construct()
    {
        parent::__construct('Credenciales inválidas');
    }
}
