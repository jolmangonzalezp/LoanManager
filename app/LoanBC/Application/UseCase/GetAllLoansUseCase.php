<?php

declare(strict_types=1);

namespace App\LoanBC\Application\UseCase;

use App\CustomerBC\Infrastructure\Persistence\Model\CustomerModel;
use App\LoanBC\Application\DTO\LoanResponse;
use App\LoanBC\Domain\Repository\CustomerNameProvider;
use App\LoanBC\Domain\Repository\LoanFinderAll;
use App\LoanBC\Infrastructure\Persistence\Model\LoanModel;
use App\LoanBC\Infrastructure\Persistence\Model\LoanTypeModel;
use App\RouteBC\Infrastructure\Persistence\Model\RouteModel;
use App\RouteBC\Infrastructure\Persistence\Model\ZoneModel;

final class GetAllLoansUseCase
{
    public function __construct(
        private readonly LoanFinderAll $loanFinder,
        private readonly CustomerNameProvider $customerProvider
    ) {}

    public function execute(?string $userId = null, ?string $role = null): array
    {
        $loans = $userId && $role !== 'admin'
            ? $this->getFilteredLoans($userId)
            : $this->loanFinder->findAll();

        if (empty($loans)) {
            return [];
        }

        $customerIds = array_unique(array_map(fn ($l) => $l->getCustomerId()->getValue(), $loans));

        $namesMap = $this->customerProvider->getNamesMap($customerIds);

        $loanNumbersMap = LoanModel::whereIn('id', array_map(fn ($l) => $l->getId()->getValue(), $loans))
            ->pluck('loan_number', 'id')
            ->toArray();

        $loanTypeIds = array_unique(array_map(fn ($l) => $l->getLoanTypeId()?->getValue(), $loans));
        $loanTypesMap = LoanTypeModel::whereIn('id', array_filter($loanTypeIds))
            ->pluck('name', 'id')
            ->toArray();

        return array_map(function ($loan) use ($namesMap, $loanNumbersMap, $loanTypesMap) {
            $loanTypeName = $loanTypesMap[$loan->getLoanTypeId()?->getValue()] ?? null;
            $response = LoanResponse::fromEntity($loan, $loanTypeName);
            $cid = $loan->getCustomerId()->getValue();
            $loanId = $loan->getId()->getValue();

            if (isset($namesMap[$cid])) {
                $response->setCustomerName($namesMap[$cid]);
            }

            if (isset($loanNumbersMap[$loanId])) {
                $response->setLoanNumber($loanNumbersMap[$loanId]);
            }

            return $response->toArray($cid);
        }, $loans);
    }

    private function getFilteredLoans(string $userId): array
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

        $customerIds = CustomerModel::whereIn('route_id', $routeIds)
            ->whereIn('zone_id', $mappedZoneIds)
            ->pluck('id')
            ->toArray();

        if (empty($customerIds)) {
            return [];
        }

        $allLoans = $this->loanFinder->findAll();

        return array_values(array_filter(
            $allLoans,
            fn ($l) => in_array($l->getCustomerId()->getValue(), $customerIds)
        ));
    }
}
