<?php

declare(strict_types=1);

namespace App\LoanBC\Application\UseCase;

use App\LoanBC\Application\DTO\LoanReportResponse;
use App\LoanBC\Domain\Repository\LoanFinderAll;
use App\LoanBC\Domain\ValueObject\LoanStatus;

final class GetLoanReportUseCase
{
    public function __construct(
        private readonly LoanFinderAll $finder
    ) {}

    public function execute(): LoanReportResponse
    {
        $loans = $this->finder->findAll();

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
}
