<?php

declare(strict_types=1);

namespace App\SharedKernel\Application\Exception;

final class NotFoundException extends ApplicationException
{
    public function __construct(string $message = 'Recurso no encontrado')
    {
        parent::__construct($message);
    }
}
