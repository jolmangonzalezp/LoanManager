<?php

declare(strict_types=1);

namespace App\CustomerBC\Application\Exceptions;

use App\SharedKernel\Application\Exception\ApplicationException;

class CustomerNotFoundException extends ApplicationException
{
    public function __construct()
    {
        parent::__construct('Cliente no encontrado');
    }
}
