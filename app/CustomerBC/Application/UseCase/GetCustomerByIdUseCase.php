<?php

declare(strict_types=1);

namespace App\CustomerBC\Application\UseCase;

use App\CustomerBC\Application\DTO\CustomerResponse;
use App\CustomerBC\Application\Exceptions\CustomerNotFoundException;
use App\CustomerBC\Domain\Repository\CustomerFinderById;
use App\CustomerBC\Domain\ValueObject\CustomerIdVO;
use App\CustomerBC\Infrastructure\Persistence\Model\CustomerModel;
use App\SharedKernel\Domain\Ports\MaskingService;

final class GetCustomerByIdUseCase
{
    public function __construct(
        private readonly CustomerFinderById $finder,
        private readonly MaskingService $masking
    ) {}

    public function execute(string $id): array
    {
        $customerId = CustomerIdVO::fromString($id);

        $customer = $this->finder->findById($customerId);

        if (! $customer) {
            throw new CustomerNotFoundException;
        }

        $model = CustomerModel::where('id', $customerId->getValue())->first(['latitude', 'longitude']);

        return CustomerResponse::fromEntity(
            $customer,
            $model?->latitude,
            $model?->longitude,
        )->toArray();
    }
}