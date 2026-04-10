<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\Exceptions;

class InvalidEmailException extends DomainException
{
    public function __construct(string $reason = 'empty')
    {
        $message = match ($reason) {
            'empty' => 'El correo electrónico es requerido',
            'missing_at' => 'El correo electrónico debe contener @',
            'empty_local' => 'La parte local del correo es requerida',
            'local_too_long' => 'La parte local del correo excede los 64 caracteres',
            'invalid_local_chars' => 'La parte local del correo contiene caracteres inválidos',
            'empty_domain' => 'El dominio del correo es requerido',
            'domain_too_long' => 'El dominio del correo excede los 255 caracteres',
            'invalid_domain_format' => 'El formato del dominio no es válido',
            default => "El correo electrónico es inválido: {$reason}"
        };
        parent::__construct($message);
    }
}
