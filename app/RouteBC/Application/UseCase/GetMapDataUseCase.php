<?php

declare(strict_types=1);

namespace App\RouteBC\Application\UseCase;

use App\CustomerBC\Infrastructure\Persistence\Model\CustomerModel;
use App\RouteBC\Domain\Repository\RouteRepositoryInterface;
use App\RouteBC\Domain\Repository\ZoneRepositoryInterface;
use App\RouteBC\Infrastructure\Persistence\Model\RouteModel;
use App\RouteBC\Infrastructure\Persistence\Model\ZoneModel;

final class GetMapDataUseCase
{
    private ?array $response = null;

    public function __construct(
        private readonly ZoneRepositoryInterface $zoneRepo,
        private readonly RouteRepositoryInterface $routeRepo,
    ) {}

    public function getResponse(): ?array
    {
        return $this->response;
    }

    public function execute(?string $userId = null, ?string $role = null): bool
    {
        $zonesQuery = ZoneModel::query();

        $customersQuery = CustomerModel::whereNotNull('latitude')
            ->whereNotNull('longitude');

        if ($userId && $role !== 'admin') {
            $routeIds = RouteModel::whereHas('users', fn ($q) => $q->where('user_id', $userId))
                ->pluck('id')
                ->toArray();

            if (! empty($routeIds)) {
                $zoneIds = RouteModel::whereIn('id', $routeIds)
                    ->pluck('zone_id')
                    ->unique()
                    ->toArray();
                $zonesQuery->whereIn('id', $zoneIds);
                $customersQuery->whereIn('route_id', $routeIds);
            } else {
                $zonesQuery->whereRaw('1 = 0');
                $customersQuery->whereRaw('1 = 0');
            }
        }

        $zones = $zonesQuery->get();
        $customers = $customersQuery->get(['id', 'latitude', 'longitude', 'zone_id', 'route_id']);

        $this->response = [
            'zones' => $zones->map(fn ($z) => [
                'id' => $z->id,
                'name' => $z->name,
                'polygon' => $z->polygon,
            ])->values()->toArray(),
            'customers' => $customers->map(fn ($c) => [
                'id' => $c->id,
                'latitude' => (float) $c->latitude,
                'longitude' => (float) $c->longitude,
                'zone_id' => $c->zone_id,
                'route_id' => $c->route_id,
            ])->values()->toArray(),
        ];

        return true;
    }
}
