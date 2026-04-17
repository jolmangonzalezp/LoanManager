<?php

declare(strict_types=1);

namespace App\LoanBC\Domain\Services;

use App\SharedKernel\Domain\ValueObject\DateVO;
use App\SharedKernel\Domain\ValueObject\MoneyVO;

interface InterestCalculator
{
    public function calculateMonthlyInterest(
        MoneyVO $capital,
        float $annualRate
    ): MoneyVO;

    public function calculateInterestFromDate(
        MoneyVO $capital,
        float $annualRate,
        DateVO $fromDate
    ): MoneyVO;

    public function isInDefault(DateVO $dueDate): bool;

    public function getDefaultDays(DateVO $dueDate): int;
}
