<?php

declare(strict_types=1);

namespace App\CustomerBC\Domain\Repository;

interface CustomerDniFinder
{
    public function existsByDni(string $dniHash, ?string $excludeCustomerId = null): bool;
}