<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\Exception;

class InvalidDniException extends DomainException
{
    public function __construct(string $message = '', \Throwable $previous = null)
    {
        parent::__construct($message, $previous);
    }
}
