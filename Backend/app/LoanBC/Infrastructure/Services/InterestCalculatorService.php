<?php

declare(strict_types=1);

namespace App\LoanBC\Infrastructure\Services;

use App\LoanBC\Domain\Services\InterestCalculator;
use App\SharedKernel\Domain\ValueObjects\DateVO;
use App\SharedKernel\Domain\ValueObjects\MoneyVO;

final class InterestCalculatorService implements InterestCalculator
{
    public function calculateMonthlyInterest(
        MoneyVO $capital,
        float $annualRate
    ): MoneyVO {
        $monthlyRate = $annualRate / 12;
        $interestAmount = (int) round(
            $capital->getAmount() * $monthlyRate / 100
        );

        return MoneyVO::create($interestAmount, $capital->getCurrency());
    }

    public function calculateInterestFromDate(
        MoneyVO $capital,
        float $annualRate,
        DateVO $fromDate
    ): MoneyVO {
        $today = DateVO::now();
        $monthsSinceStart = $this->calculateMonthsDifference($fromDate, $today);

        if ($monthsSinceStart <= 0) {
            return MoneyVO::zero();
        }

        $monthlyInterest = $this->calculateMonthlyInterest($capital, $annualRate);
        $totalInterestAmount = $monthlyInterest->getAmount() * $monthsSinceStart;

        return MoneyVO::create($totalInterestAmount, $capital->getCurrency());
    }

    public function isInDefault(DateVO $dueDate): bool
    {
        return DateVO::now()->isAfter($dueDate);
    }

    public function getDefaultDays(DateVO $dueDate): int
    {
        if (! $this->isInDefault($dueDate)) {
            return 0;
        }

        $today = DateVO::now();

        return $today->diffInDays($dueDate);
    }

    private function calculateMonthsDifference(DateVO $from, DateVO $to): int
    {
        $fromYear = $from->getYear();
        $fromMonth = $from->getMonth();
        $toYear = $to->getYear();
        $toMonth = $to->getMonth();

        return (($toYear - $fromYear) * 12) + ($toMonth - $fromMonth);
    }
}
