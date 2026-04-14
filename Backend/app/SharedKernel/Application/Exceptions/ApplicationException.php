<?php

declare(strict_types=1);

namespace App\SharedKernel\Application\Exceptions;

use Throwable;

abstract class ApplicationException extends \Exception
{
    public function __construct(
        string $message = '',
        ?Throwable $previous = null
    ) {
        parent::__construct($message, 0, $previous);
    }
}