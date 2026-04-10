<?php

declare(strict_types=1);

namespace App\CustomerBC\Infrastructure\Repository;

use App\CustomerBC\Domain\Repositories\CustomerDniFinder;
use App\CustomerBC\Infrastructure\Models\CustomerModel;
use App\SharedKernel\Domain\ValueObjects\DniType;
use App\SharedKernel\Infrastructure\Services\LaravelEncryptionService;

final class EloquentCustomerDniFinder implements CustomerDniFinder
{
    public function __construct(
        private readonly LaravelEncryptionService $encryption
    ) {}

    public function existsByDni(string $dniNumber, DniType $dniType): bool
    {
        $hashedDni = $this->encryption->hash($dniNumber);

        return CustomerModel::where('dni_type', $dniType->value)
            ->where('dni_hash', $hashedDni)
            ->exists();
    }
}
