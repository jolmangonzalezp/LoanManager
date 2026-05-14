<?php

declare(strict_types=1);

namespace App\CustomerBC\Application\UseCase;

use App\CustomerBC\Application\DTO\CustomerResponse;
use App\CustomerBC\Domain\Repository\CustomerFinderAll;
use App\CustomerBC\Infrastructure\Mapper\CustomerMapper;
use App\CustomerBC\Infrastructure\Persistence\Model\CustomerModel;
use App\RouteBC\Infrastructure\Persistence\Model\RouteModel;
use App\RouteBC\Infrastructure\Persistence\Model\ZoneModel;

final class GetAllCustomersUseCase
{
    public function __construct(
        private readonly CustomerFinderAll $finder,
        private readonly CustomerMapper $mapper,
    ) {}

    public function execute(?string $userId = null, ?string $role = null): array
    {
        $customers = match (true) {
            $userId && $role !== 'admin' => $this->getFilteredCustomers($userId),
            default => $this->finder->findAll(),
        };

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

    private function getFilteredCustomers(string $userId): array
    {
        $routeIds = RouteModel::whereHas('users', fn ($q) => $q->where('user_id', $userId))
            ->pluck('id')
            ->toArray();

        if (empty($routeIds)) {
            return [];
        }

        $zoneIds = RouteModel::whereIn('id', $routeIds)
            ->pluck('zone_id')
            ->unique()
            ->filter()
            ->toArray();

        if (empty($zoneIds)) {
            return [];
        }

        $mappedZoneIds = ZoneModel::whereIn('id', $zoneIds)
            ->whereNotNull('polygon')
            ->pluck('id')
            ->toArray();

        if (empty($mappedZoneIds)) {
            return [];
        }

        return CustomerModel::whereIn('route_id', $routeIds)
            ->whereIn('zone_id', $mappedZoneIds)
            ->get()
            ->map(fn ($model) => $this->mapper->toDomain($model))
            ->all();
    }
}
