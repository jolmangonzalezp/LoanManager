<?php

declare(strict_types=1);

namespace App\RouteBC\Application\UseCase;

use App\CustomerBC\Infrastructure\Persistence\Model\CustomerModel;
use App\RouteBC\Domain\Repository\RouteRepositoryInterface;
use App\RouteBC\Domain\Repository\ZoneRepositoryInterface;
use App\RouteBC\Domain\ValueObject\ZoneIdVO;

final class DeleteZoneUseCase
{
    public function __construct(
        private readonly ZoneRepositoryInterface $zoneRepo,
        private readonly RouteRepositoryInterface $routeRepo,
    ) {}

    public function execute(ZoneIdVO $id): bool
    {
        $routes = $this->routeRepo->findByZoneId($id);

        foreach ($routes as $route) {
            $this->routeRepo->delete($route->getId());
        }

        CustomerModel::where('zone_id', $id->getValue())
            ->update(['zone_id' => null, 'route_id' => null]);

        $this->zoneRepo->delete($id);

        return true;
    }
}
