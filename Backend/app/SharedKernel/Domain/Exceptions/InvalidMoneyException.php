<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\Exceptions;

class InvalidMoneyException extends DomainException
{
    public function __construct(string $reason)
    {
        $message = match ($reason) {
            'negative' => 'El monto no puede ser negativo',
            'would_be_negative' => 'El resultado de la operación sería negativo',
            default => "El monto es inválido: {$reason}"
        };
        parent::__construct($message);
    }
}
