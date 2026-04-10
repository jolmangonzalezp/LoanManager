<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\Exceptions;

class InvalidPhoneException extends DomainException
{
    public function __construct(string $reason)
    {
        $message = match ($reason) {
            'empty' => 'El número de teléfono es requerido',
            'invalid_country_code' => 'El código de país no es válido',
            'too_short' => 'El número de teléfono debe tener entre 7 y 15 dígitos',
            'too_long' => 'El número de teléfono debe tener entre 7 y 15 dígitos',
            default => "El número de teléfono es inválido: {$reason}"
        };
        parent::__construct($message);
    }
}
