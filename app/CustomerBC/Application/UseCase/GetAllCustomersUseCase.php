<?php

declare(strict_types=1);

namespace App\CustomerBC\Application\UseCase;

use App\CustomerBC\Application\DTO\CustomerResponse;
use App\CustomerBC\Domain\Repository\CustomerFinderAll;
use App\CustomerBC\Infrastructure\Persistence\Model\CustomerModel;

final class GetAllCustomersUseCase
{
    public function __construct(
        private readonly CustomerFinderAll $finder,
    ) {}

    public function execute(): array
    {
        $customers = $this->finder->findAll();

        $ids = array_map(fn ($c) => $c->getId()->getValue(), $customers);
        $latLngMap = [];
        if (! empty($ids)) {
            $models = CustomerModel::whereIn('id', $ids)->get(['id', 'latitude', 'longitude']);
            foreach ($models as $m) {
                $latLngMap[$m->id] = ['lat' => $m->latitude, 'lng' => $m->longitude];
            }
        }

        return array_map(
            fn ($customer) => CustomerResponse::fromEntity(
                $customer,
                $latLngMap[$customer->getId()->getValue()]['lat'] ?? null,
                $latLngMap[$customer->getId()->getValue()]['lng'] ?? null,
            )->toArray(),
            $customers
        );
    }
}
