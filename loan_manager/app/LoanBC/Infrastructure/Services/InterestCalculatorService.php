<?php

declare(strict_types=1);

namespace App\LoanBC\Infrastructure\Services;

use App\LoanBC\Domain\Services\InterestCalculator;
use App\SharedKernel\Domain\ValueObject\DateVO;
use App\SharedKernel\Domain\ValueObject\MoneyVO;

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

        if ($interestAmount <= 0) {
            return MoneyVO::zero();
        }

        return MoneyVO::create($interestAmount);
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

        if ($totalInterestAmount <= 0) {
            return MoneyVO::zero();
        }

        return MoneyVO::create($totalInterestAmount);
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
        $from = $dueDate->getValue();
        $to = $today->getValue();

        return (int) $from->diff($to)->days;
    }

    private function calculateMonthsDifference(DateVO $from, DateVO $to): int
    {
        $fromDt = $from->getValue();
        $toDt = $to->getValue();
        $fromYear = (int) $fromDt->format('Y');
        $fromMonth = (int) $fromDt->format('n');
        $toYear = (int) $toDt->format('Y');
        $toMonth = (int) $toDt->format('n');

        return (($toYear - $fromYear) * 12) + ($toMonth - $fromMonth);
    }
}
