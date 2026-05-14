<?php

declare(strict_types=1);

namespace App\RouteBC\Application\UseCase;

use App\RouteBC\Application\CQRS\Command\CreateRouteCommand;
use App\RouteBC\Application\DTO\RouteResponse;
use App\RouteBC\Domain\Aggregate\Route;
use App\RouteBC\Domain\Repository\RouteRepositoryInterface;
use App\RouteBC\Domain\ValueObject\RouteIdVO;

final class CreateRouteUseCase
{
    private ?array $response = null;

    public function __construct(
        private readonly RouteRepositoryInterface $routeRepo
    ) {}

    public function getResponse(): ?array
    {
        return $this->response;
    }

    public function execute(CreateRouteCommand $command): bool
    {
        $route = Route::create(
            RouteIdVO::generate(),
            $command->name,
            $command->zoneId,
        );

        $this->routeRepo->save($route);

        $this->response = RouteResponse::fromEntity($route)->toArray();

        return true;
    }
}
