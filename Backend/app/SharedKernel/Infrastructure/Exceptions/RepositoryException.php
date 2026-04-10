<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\Exceptions;

class RepositoryException extends InfrastructureException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
