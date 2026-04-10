<?php

declare(strict_types=1);

namespace App\CustomerBC\Domain\Repositories;

use App\SharedKernel\Domain\ValueObjects\DniType;

interface CustomerDniFinder
{
    public function existsByDni(string $dniNumber, DniType $dniType): bool;
}
