<?php

declare(strict_types=1);

namespace App\RouteBC\Application\UseCase;

use App\RouteBC\Application\CQRS\Command\AssignUsersToRouteCommand;
use App\RouteBC\Application\DTO\RouteResponse;
use App\RouteBC\Domain\Repository\RouteRepositoryInterface;

final class AssignUsersToRouteUseCase
{
    private ?array $response = null;

    public function __construct(
        private readonly RouteRepositoryInterface $routeRepo
    ) {}

    public function getResponse(): ?array
    {
        return $this->response;
    }

    public function execute(AssignUsersToRouteCommand $command): bool
    {
        $this->routeRepo->assignUsers($command->routeId, $command->userIds);

        $route = $this->routeRepo->findById($command->routeId);

        if ($route) {
            $this->response = RouteResponse::fromEntity($route)->toArray();
        }

        return true;
    }
}
