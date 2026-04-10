<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\Exceptions;

class InvalidDniException extends DomainException
{
    public function __construct(string $reason)
    {
        $message = match ($reason) {
            'empty' => 'El número de documento es requerido',
            'invalid_type' => 'El tipo de documento no es válido',
            'invalid_length' => 'La longitud del documento no es válida para el tipo especificado',
            'invalid_format' => 'El formato del documento no es válido para el tipo especificado',
            'invalid_check_digit' => 'El dígito de verificación del documento es incorrecto',
            default => "El documento es inválido: {$reason}"
        };
        parent::__construct($message);
    }
}
