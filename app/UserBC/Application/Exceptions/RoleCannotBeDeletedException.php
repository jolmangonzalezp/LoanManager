<?php

declare(strict_types=1);

namespace App\UserBC\Application\Exceptions;

use App\SharedKernel\Application\Exception\ApplicationException;

final class RoleCannotBeDeletedException extends ApplicationException
{
    public function __construct()
    {
        parent::__construct('El rol de sistema no puede ser eliminado');
    }
}
