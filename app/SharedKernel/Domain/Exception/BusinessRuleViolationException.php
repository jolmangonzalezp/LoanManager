<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\Exception;

final class BusinessRuleViolationException extends DomainException
{
    public function __construct(
        string $message = 'Violación de regla de negocio',
    ) {
        parent::__construct("{$message}");
    }
}
