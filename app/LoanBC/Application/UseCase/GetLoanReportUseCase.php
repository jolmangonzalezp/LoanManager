<?php

declare(strict_types=1);

namespace App\LoanBC\Application\UseCase;

use App\LoanBC\Application\DTO\LoanReportResponse;
use App\CustomerBC\Infrastructure\Persistence\Model\CustomerModel;
use App\LoanBC\Domain\Repository\LoanFinderAll;
use App\LoanBC\Domain\ValueObject\LoanStatus;
use App\RouteBC\Infrastructure\Persistence\Model\RouteModel;
use App\RouteBC\Infrastructure\Persistence\Model\ZoneModel;

final class GetLoanReportUseCase
{
    public function __construct(
        private readonly LoanFinderAll $finder
    ) {}

    public function execute(?string $userId = null, ?string $role = null): LoanReportResponse
    {
        $loans = $userId && $role !== 'admin'
            ? $this->getFilteredLoans($userId)
            : $this->finder->findAll();

        $totalLoans = count($loans);
        $activeLoans = 0;
        $paidLoans = 0;
        $defaultedLoans = 0;
        $totalCapital = 0;
        $totalRemainingDebt = 0;
        $totalPaidCapital = 0;
        $totalPaidInterest = 0;

        foreach ($loans as $loan) {
            $status = $loan->getStatus();

            if ($status === LoanStatus::ACTIVE) {
                $activeLoans++;
            } elseif ($status === LoanStatus::PAID) {
                $paidLoans++;
            } elseif ($status === LoanStatus::DEFAULTED) {
                $defaultedLoans++;
            }

            $totalCapital += $loan->getOriginalCapital()->getAmount();
            $totalRemainingDebt += $loan->getRemainingDebt()->getAmount();
            $totalPaidCapital += $loan->getPaidCapital()->getAmount();
            $totalPaidInterest += $loan->getPaidInterest()->getAmount();
        }

        return new LoanReportResponse(
            totalLoans: $totalLoans,
            activeLoans: $activeLoans,
            paidLoans: $paidLoans,
            defaultedLoans: $defaultedLoans,
            totalCapital: $totalCapital,
            totalRemainingDebt: $totalRemainingDebt,
            totalPaidCapital: $totalPaidCapital,
            totalPaidInterest: $totalPaidInterest
        );
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

        $allLoans = $this->finder->findAll();

        return array_values(array_filter(
            $allLoans,
            fn ($l) => in_array($l->getCustomerId()->getValue(), $customerIds)
        ));
    }
}
