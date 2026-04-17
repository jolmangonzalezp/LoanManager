<?php

declare(strict_types=1);

namespace App\CustomerBC\Application\UseCase;

use App\CustomerBC\Application\DTO\CustomerSummaryResponse;
use App\CustomerBC\Domain\Repository\CustomerFinderAll;
use App\SharedKernel\Domain\Ports\MaskingService;

final class GetAllCustomersSummaryUseCase
{
    public function __construct(
        private readonly CustomerFinderAll $finder,
        private readonly MaskingService $masking
    ) {}

    public function execute(): array
    {
        $customers = $this->finder->findAll();

        return array_map(
            fn ($customer) => CustomerSummaryResponse::fromEntity($customer)->toArray($this->masking),
            $customers
        );
    }
}