<?php

declare(strict_types=1);

namespace App\CustomerBC\Application\UseCases;

use App\CustomerBC\Application\DTOs\CustomerResponse;
use App\CustomerBC\Domain\Repositories\CustomerFinderAll;

final class GetAllCustomersUseCase
{
    public function __construct(
        private readonly CustomerFinderAll $finder
    ) {}

    public function execute(): array
    {
        $customers = $this->finder->findAll();

        return array_map(
            fn ($customer) => CustomerResponse::fromEntity($customer),
            $customers
        );
    }
}
