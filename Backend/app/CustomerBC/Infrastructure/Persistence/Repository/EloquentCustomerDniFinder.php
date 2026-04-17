<?php

declare(strict_types=1);

namespace App\CustomerBC\Infrastructure\Persistence\Repository;

use App\CustomerBC\Domain\Repository\CustomerDniFinder;
use App\CustomerBC\Infrastructure\Persistence\Model\CustomerModel;
use App\SharedKernel\Infrastructure\Exception\DatabaseException;
use Illuminate\Database\QueryException;

final class EloquentCustomerDniFinder implements CustomerDniFinder
{
    public function existsByDni(string $dniHash, ?string $excludeCustomerId = null): bool
    {
        try {
            $query = CustomerModel::where('dni_hash', $dniHash);

            if ($excludeCustomerId !== null) {
                $query->where('id', '!=', $excludeCustomerId);
            }

            return $query->exists();
        } catch (QueryException $e) {
            throw new DatabaseException('buscar', 'customers');
        }
    }
}