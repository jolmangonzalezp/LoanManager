<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\Exceptions;

use Throwable;

abstract class InfrastructureException extends \Exception
{
    public function __construct(
        string $message = '',
        ?Throwable $previous = null
    ) {
        parent::__construct($message, 0, $previous);
    }
}