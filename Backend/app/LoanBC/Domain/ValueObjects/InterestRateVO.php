<?php

declare(strict_types=1);

namespace App\LoanBC\Domain\ValueObjects;

use App\SharedKernel\Domain\Exceptions\DomainException;
use App\SharedKernel\Domain\ValueObjects\MoneyVO;

final class InterestRateVO implements \Stringable
{
    private const MIN_RATE = 0.0;

    private const MAX_RATE = 100.0;

    private const DECIMALS = 4;

    private function __construct(
        private readonly float $annualRate,
        private readonly float $monthlyRate
    ) {}

    public static function createAnnual(float $annualRate): self
    {
        if ($annualRate < self::MIN_RATE || $annualRate > self::MAX_RATE) {
            throw new DomainException(
                'invalid_rate',
                'La tasa de interés debe estar entre 0% y 100%'
            );
        }

        $monthlyRate = self::calculateMonthlyRate($annualRate);

        return new self($annualRate, $monthlyRate);
    }

    public static function createMonthly(float $monthlyRate): self
    {
        if ($monthlyRate < self::MIN_RATE || $monthlyRate > self::MAX_RATE) {
            throw new DomainException(
                'invalid_rate',
                'La tasa de interés debe estar entre 0% y 100%'
            );
        }

        $annualRate = self::calculateAnnualRate($monthlyRate);

        return new self($annualRate, $monthlyRate);
    }

    private static function calculateMonthlyRate(float $annualRate): float
    {
        return $annualRate / 12;
    }

    private static function calculateAnnualRate(float $monthlyRate): float
    {
        return $monthlyRate * 12;
    }

    public function getAnnualRate(): float
    {
        return $this->annualRate;
    }

    public function getMonthlyRate(): float
    {
        return $this->monthlyRate;
    }

    public function calculateInterest(MoneyVO $capital): MoneyVO
    {
        $interestAmount = (int) round(
            $capital->getAmount() * $this->monthlyRate / 100
        );

        return MoneyVO::create($interestAmount, $capital->getCurrency());
    }

    public function calculateMonthlyInterestFromCapital(int $capitalAmount): int
    {
        return (int) round($capitalAmount * $this->monthlyRate / 100);
    }

    public function equals(self $other): bool
    {
        return $this->annualRate === $other->annualRate;
    }

    public function __toString(): string
    {
        return sprintf('%.2f%% EA / %.4f%% EM', $this->annualRate, $this->monthlyRate);
    }
}
