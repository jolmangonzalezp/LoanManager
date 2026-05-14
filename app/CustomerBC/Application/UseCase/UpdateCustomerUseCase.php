<?php

declare(strict_types=1);

namespace App\CustomerBC\Application\UseCase;

use App\CustomerBC\Application\CQRS\Command\UpdateCustomerCommand;
use App\CustomerBC\Application\DTO\CustomerResponse;
use App\CustomerBC\Application\Exceptions\CustomerAlreadyExistsException;
use App\CustomerBC\Application\Exceptions\CustomerNotFoundException;
use App\CustomerBC\Domain\Aggregate\Customer;
use App\CustomerBC\Domain\Repository\CustomerDniFinder;
use App\CustomerBC\Domain\Repository\CustomerFinderById;
use App\CustomerBC\Domain\Repository\CustomerUpdater;
use App\CustomerBC\Domain\ValueObject\CustomerIdVO;
use App\CustomerBC\Infrastructure\Persistence\Model\CustomerModel;
use App\RouteBC\Domain\Repository\RouteRepositoryInterface;
use App\RouteBC\Domain\Service\GeoLocationService;

final class UpdateCustomerUseCase
{
    private ?array $response = null;

    public function __construct(
        private readonly CustomerFinderById $finder,
        private readonly CustomerUpdater $updater,
        private readonly CustomerDniFinder $dniFinder,
        private readonly GeoLocationService $geoLocationService,
        private readonly RouteRepositoryInterface $routeRepo,
    ) {}

    public function getResponse(): ?array
    {
        return $this->response;
    }

    public function execute(UpdateCustomerCommand $command): bool
    {
        $customerId = CustomerIdVO::fromString($command->id->getValue());
        $existingCustomer = $this->finder->findById($customerId);

        if (! $existingCustomer) {
            throw new CustomerNotFoundException;
        }

        $newDni = $command->personalData->getDni();
        $existingDni = $existingCustomer->getPersonalData()->getDni();

        if ($newDni->getNumber() !== $existingDni->getNumber() ||
            $newDni->getType() !== $existingDni->getType()) {
            if ($this->dniFinder->existsByDni(
                $newDni->getHash(),
                $customerId->getValue()
            )) {
                throw new CustomerAlreadyExistsException;
            }
        }

        $updatedCustomer = Customer::reconstitute(
            $customerId,
            $command->personalData,
            $existingCustomer->getCreatedAt(),
            $existingCustomer->isEnabled()
        );

        $this->updater->update($updatedCustomer);

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
                if (! empty($routes)) {
                    $updateData['route_id'] = $routes[0]->getId()->getValue();
                }
            } else {
                $updateData['zone_id'] = null;
                $updateData['route_id'] = null;
            }

            CustomerModel::where('id', $customerId->getValue())->update($updateData);
        }

        $this->response = CustomerResponse::fromEntity(
            $updatedCustomer,
            $command->latitude,
            $command->longitude,
        )->toArray();

        return true;
    }
}
