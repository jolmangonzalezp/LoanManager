<?php

declare(strict_types=1);

namespace App\RouteBC\Application\UseCase;

use App\RouteBC\Application\CQRS\Command\CreateZoneCommand;
use App\RouteBC\Application\DTO\ZoneResponse;
use App\RouteBC\Domain\Aggregate\Zone;
use App\RouteBC\Domain\Repository\ZoneRepositoryInterface;
use App\RouteBC\Domain\ValueObject\Polygon;
use App\RouteBC\Domain\ValueObject\ZoneIdVO;

final class CreateZoneUseCase
{
    private ?array $response = null;

    public function __construct(
        private readonly ZoneRepositoryInterface $zoneRepo
    ) {}

    public function getResponse(): ?array
    {
        return $this->response;
    }

    public function execute(CreateZoneCommand $command): bool
    {
        $zone = Zone::create(
            ZoneIdVO::generate(),
            $command->name,
            Polygon::fromArray($command->polygon)
        );

        $this->zoneRepo->save($zone);

        $this->response = ZoneResponse::fromEntity($zone)->toArray();

        return true;
    }
}
