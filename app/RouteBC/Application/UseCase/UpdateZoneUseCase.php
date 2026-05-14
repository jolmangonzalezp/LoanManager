<?php

declare(strict_types=1);

namespace App\RouteBC\Application\UseCase;

use App\CustomerBC\Infrastructure\Persistence\Model\CustomerModel;
use App\RouteBC\Application\CQRS\Command\UpdateZoneCommand;
use App\RouteBC\Application\DTO\ZoneResponse;
use App\RouteBC\Domain\Aggregate\Zone;
use App\RouteBC\Domain\Repository\RouteRepositoryInterface;
use App\RouteBC\Domain\Repository\ZoneRepositoryInterface;
use App\RouteBC\Domain\Service\GeoLocationService;
use App\RouteBC\Domain\ValueObject\Polygon;

final class UpdateZoneUseCase
{
    private ?array $response = null;

    public function __construct(
        private readonly ZoneRepositoryInterface $zoneRepo,
        private readonly RouteRepositoryInterface $routeRepo,
        private readonly GeoLocationService $geoLocationService,
    ) {}

    public function getResponse(): ?array
    {
        return $this->response;
    }

    public function execute(UpdateZoneCommand $command): bool
    {
        $zone = $this->zoneRepo->findById($command->id);

        if ($zone === null) {
            throw new \RuntimeException('Zone not found');
        }

        $zone = $zone->withName($command->name)->withPolygon(Polygon::fromArray($command->polygon));

        $this->zoneRepo->save($zone);

        $this->reassignCustomersInZone($zone);

        $this->response = ZoneResponse::fromEntity($zone)->toArray();

        return true;
    }

    private function reassignCustomersInZone(Zone $zone): void
    {
        $customers = CustomerModel::whereNull('zone_id')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        $routes = $this->routeRepo->findByZoneId($zone->getId());

        foreach ($customers as $customer) {
            $matchedZone = $this->geoLocationService->pointInWhichZone(
                (float) $customer->latitude,
                (float) $customer->longitude
            );

            if ($matchedZone && $matchedZone->getId()->getValue() === $zone->getId()->getValue()) {
                $routeId = ! empty($routes) ? $routes[0]->getId()->getValue() : null;

                $customer->update([
                    'zone_id' => $zone->getId()->getValue(),
                    'route_id' => $routeId,
                ]);
            }
        }
    }
}
