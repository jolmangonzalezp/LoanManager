<?php

declare(strict_types=1);

namespace App\CustomerBC\Application\UseCase;

use App\CustomerBC\Application\CQRS\Command\CreateCustomerCommand;
use App\CustomerBC\Application\DTO\CreatedCustomerResponse;
use App\CustomerBC\Application\Exceptions\CustomerAlreadyExistsException;
use App\CustomerBC\Domain\Aggregate\Customer;
use App\CustomerBC\Domain\Repository\CustomerCreator;
use App\CustomerBC\Domain\Repository\CustomerDniFinder;
use App\CustomerBC\Infrastructure\Persistence\Model\CustomerModel;
use App\RouteBC\Domain\Service\GeoLocationService;
use App\RouteBC\Domain\Repository\RouteRepositoryInterface;

final class CreateCustomerUseCase
{
    private ?CreatedCustomerResponse $response = null;

    public function __construct(
        private readonly CustomerCreator $creator,
        private readonly CustomerDniFinder $dniFinder,
        private readonly GeoLocationService $geoLocationService,
        private readonly RouteRepositoryInterface $routeRepo,
    ) {}

    public function getResponse(): ?CreatedCustomerResponse
    {
        return $this->response;
    }

    public function execute(CreateCustomerCommand $command): bool
    {
        $dni = $command->personalData->getDni();

        if ($this->dniFinder->existsByDni($dni->getHash())) {
            throw new CustomerAlreadyExistsException;
        }

        $customer = Customer::create($command->personalData);

        $this->creator->create($customer);

        if ($command->latitude !== null && $command->longitude !== null) {
            $zone = $this->geoLocationService->pointInWhichZone(
                $command->latitude,
                $command->longitude
            );

            $updateData = [
                'latitude' => $command->latitude,
                'longitude' => $command->longitude,
            ];

            if ($zone) {
                $routes = $this->routeRepo->findByZoneId($zone->getId());
                $updateData['zone_id'] = $zone->getId()->getValue();
                if (!empty($routes)) {
                    $updateData['route_id'] = $routes[0]->getId()->getValue();
                }
            }

            CustomerModel::where('id', $customer->getId()->getValue())->update($updateData);
        }

        $this->response = CreatedCustomerResponse::fromEntity($customer);

        return true;
    }
}
