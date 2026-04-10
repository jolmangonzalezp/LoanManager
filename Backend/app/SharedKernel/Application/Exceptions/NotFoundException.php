<?php

declare(strict_types=1);

namespace App\SharedKernel\Application\Exceptions;

class NotFoundException extends ApplicationException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
