<?php

declare(strict_types=1);

namespace App\UserBC\Application\Exceptions;

use App\SharedKernel\Application\Exception\ApplicationException;

class UserNotFoundException extends ApplicationException
{
    public function __construct()
    {
        parent::__construct('Usuario no encontrado');
    }
}
