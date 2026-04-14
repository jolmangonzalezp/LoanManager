<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\Exceptions;

class InvalidNameException extends DomainException
{
    public function __construct(string $message, \Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
