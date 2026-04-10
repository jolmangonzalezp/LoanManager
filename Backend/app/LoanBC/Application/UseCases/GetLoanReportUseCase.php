<?php

declare(strict_types=1);

namespace App\LoanBC\Application\UseCases;

use App\LoanBC\Application\DTOs\LoanReportResponse;
use App\LoanBC\Domain\Repositories\LoanFinderAll;
use App\LoanBC\Domain\ValueObjects\LoanStatus;

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
