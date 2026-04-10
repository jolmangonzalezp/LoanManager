<?php

declare(strict_types=1);

namespace App\CustomerBC\Application\UseCases;

use App\CustomerBC\Application\DTOs\CustomerSummaryResponse;
use App\CustomerBC\Domain\Repositories\CustomerFinderAll;

final class GetAllCustomersSummaryUseCase
{
    public function __construct(
        private readonly CustomerFinderAll $finder
    ) {}

    public function execute(): array
    {
        $customers = $this->finder->findAll();

        return array_map(
            fn ($customer) => CustomerSummaryResponse::fromEntity($customer),
            $customers
        );
    }
}
