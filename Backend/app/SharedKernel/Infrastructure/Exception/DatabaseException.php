<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\Exception;

class DatabaseException extends InfrastructureException
{
    public function __construct(string $operation, string $table)
    {
        $message = "Error al {$operation} datos en {$table}";
        parent::__construct($message);
    }
}
