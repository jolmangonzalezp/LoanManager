<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\Exceptions;

class InvalidNameException extends DomainException
{
    public function __construct(string $reason)
    {
        $message = match ($reason) {
            'empty' => 'El nombre es requerido',
            'too_short' => 'El nombre debe tener al menos 2 caracteres',
            'invalid_chars' => 'El nombre contiene caracteres inválidos',
            default => "El nombre es inválido: {$reason}"
        };
        parent::__construct($message);
    }
}
