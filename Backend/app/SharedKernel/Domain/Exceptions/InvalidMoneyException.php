<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\Exceptions;

class InvalidMoneyException extends DomainException
{
    public function __construct(string $mmessage = '', \Throwable $previous = null)
    {
        parent::__construct($mmessage,0, $previous);
    }
}
