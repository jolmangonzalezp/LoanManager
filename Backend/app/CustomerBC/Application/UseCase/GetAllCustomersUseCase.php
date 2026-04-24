<?php

declare(strict_types=1);

namespace App\CustomerBC\Application\UseCase;

use App\CustomerBC\Application\DTO\CustomerResponse;
use App\CustomerBC\Domain\Repository\CustomerFinderAll;
use App\SharedKernel\Domain\Ports\MaskingService;

final class GetAllCustomersUseCase
{
    public function __construct(
        private readonly CustomerFinderAll $finder,
    ) {}

    public function execute(): array
    {
        $customers = $this->finder->findAll();

        return array_map(
            fn ($customer) => CustomerResponse::fromEntity($customer)->toArray(),
            $customers
        );
    }
}
