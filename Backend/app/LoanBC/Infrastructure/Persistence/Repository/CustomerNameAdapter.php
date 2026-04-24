<?php

declare(strict_types=1);

namespace App\LoanBC\Infrastructure\Persistence\Repository;

use App\LoanBC\Domain\Repository\CustomerNameProvider;
use App\CustomerBC\Application\UseCase\GetCustomerNamesUseCase;

final class CustomerNameAdapter implements CustomerNameProvider
{
    public function __construct(
        private readonly GetCustomerNamesUseCase $customerUseCase
    ) {}

    public function getNamesMap(array $customerIds): array
    {
        $data = $this->customerUseCase->execute($customerIds);
        
        return array_column($data, 'full_name', 'id');
    }
}