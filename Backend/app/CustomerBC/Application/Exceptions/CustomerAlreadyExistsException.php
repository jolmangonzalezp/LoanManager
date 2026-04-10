<?php

declare(strict_types=1);

namespace App\CustomerBC\Application\Exceptions;

use App\SharedKernel\Application\Exceptions\ApplicationException;

class CustomerAlreadyExistsException extends ApplicationException
{
    public function __construct()
    {
        parent::__construct('El cliente ya existe');
    }
}
