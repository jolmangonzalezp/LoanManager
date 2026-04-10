<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\Exceptions;

class InvalidAddressException extends DomainException
{
    public function __construct(string $reason)
    {
        $message = match ($reason) {
            'empty' => 'La dirección es requerida',
            'too_short' => 'La dirección es muy corta',
            default => "La dirección es inválida: {$reason}"
        };
        parent::__construct($message);
    }
}
