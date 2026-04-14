<?php

declare(strict_types=1);

namespace App\LoanBC\Domain\Entities;

use App\CustomerBC\Domain\ValueObjects\CustomerIdVO;
use App\LoanBC\Domain\ValueObjects\InterestRateVO;
use App\LoanBC\Domain\ValueObjects\LoanIdVO;
use App\LoanBC\Domain\ValueObjects\LoanStatus;
use App\SharedKernel\Domain\Exceptions\DomainException;
use App\SharedKernel\Domain\ValueObjects\DateVO;
use App\SharedKernel\Domain\ValueObjects\MoneyVO;

final class Loan
{
    private function __construct(
        private readonly LoanIdVO $id,
        private readonly CustomerIdVO $customerId,
        private readonly MoneyVO $originalCapital,
        private readonly InterestRateVO $interestRate,
        private readonly DateVO $startDate,
        private readonly DateVO $dueDate,
        private readonly DateVO $createdAt,
        private LoanStatus $status,
        private MoneyVO $paidInterest,
        private MoneyVO $paidCapital,
        private MoneyVO $capital,
        private MoneyVO $remainingDebt,
        private DateVO $nextPaymentDate
    ) {}

    public static function create(
        CustomerIdVO $customerId,
        MoneyVO $capital,
        InterestRateVO $interestRate,
        DateVO $startDate,
        DateVO $dueDate
    ): self {
        $interest = $interestRate->calculateInterest($capital);

        $startDateVO = $startDate->isFuture() ? DateVO::now() : $startDate;
        $nextPaymentDate = $startDateVO->addMonths(1);

        return new self(
            LoanIdVO::generate(),
            $customerId,
            $capital,
            $interestRate,
            $startDateVO,
            $dueDate,
            DateVO::now(),
            LoanStatus::ACTIVE,
            MoneyVO::zero(),
            MoneyVO::zero(),
            $capital,
            $capital,
            $nextPaymentDate
        );
    }

    public static function reconstitute(
        LoanIdVO $id,
        CustomerIdVO $customerId,
        MoneyVO $originalCapital,
        InterestRateVO $interestRate,
        DateVO $startDate,
        DateVO $dueDate,
        DateVO $createdAt,
        LoanStatus $status,
        MoneyVO $paidInterest,
        MoneyVO $paidCapital,
        MoneyVO $capital,
        MoneyVO $remainingDebt,
        DateVO $nextPaymentDate
    ): self {
        return new self(
            $id,
            $customerId,
            $originalCapital,
            $interestRate,
            $startDate,
            $dueDate,
            $createdAt,
            $status,
            $paidInterest,
            $paidCapital,
            $capital,
            $remainingDebt,
            $nextPaymentDate
        );
    }

    public function makePayment(MoneyVO $amount): self
    {
        if ($this->status !== LoanStatus::ACTIVE) {
            throw new DomainException('inactive_loan', 'El préstamo no está activo');
        }

        $monthlyInterest = $this->interestRate->calculateInterest($this->capital);

        if ($this->isInDefault()) {
            $this->capital = $this->capital->add($monthlyInterest);
            $monthlyInterest = $this->interestRate->calculateInterest($this->capital);
        }

        $newPaidInterest = $this->paidInterest->add($monthlyInterest);
        $remainingAfterInterest = $amount->subtract($monthlyInterest);

        if ($remainingAfterInterest->getAmount() > 0) {
            $capitalReduction = $remainingAfterInterest;
            $newPaidCapital = $this->paidCapital->add($capitalReduction);
            $newCapital = $this->capital->subtract($capitalReduction);
            $newRemainingDebt = $newCapital;
            $newNextPaymentDate = $this->nextPaymentDate->addMonths(1);
            $newStatus = $newCapital->isZero() ? LoanStatus::PAID : LoanStatus::ACTIVE;

            return new self(
                $this->id,
                $this->customerId,
                $this->originalCapital,
                $this->interestRate,
                $this->startDate,
                $this->dueDate,
                $this->createdAt,
                $newStatus,
                $newPaidInterest,
                $newPaidCapital,
                $newCapital,
                $newRemainingDebt,
                $newNextPaymentDate
            );
        }

        $this->paidInterest = $newPaidInterest;
        $this->nextPaymentDate = $this->nextPaymentDate->addMonths(1);

        return $this;
    }

    public function isInDefault(): bool
    {
        return DateVO::now()->isAfter($this->nextPaymentDate);
    }

    public function calculateCurrentInterest(): MoneyVO
    {
        if ($this->status !== LoanStatus::ACTIVE) {
            return MoneyVO::zero();
        }

        return $this->interestRate->calculateInterest($this->capital);
    }

    public function getId(): LoanIdVO
    {
        return $this->id;
    }

    public function getCustomerId(): CustomerIdVO
    {
        return $this->customerId;
    }

    public function getOriginalCapital(): MoneyVO
    {
        return $this->originalCapital;
    }

    public function getCapital(): MoneyVO
    {
        return $this->capital;
    }

    public function getRemainingDebt(): MoneyVO
    {
        return $this->remainingDebt;
    }

    public function getInterestRate(): InterestRateVO
    {
        return $this->interestRate;
    }

    public function getStartDate(): DateVO
    {
        return $this->startDate;
    }

    public function getDueDate(): DateVO
    {
        return $this->dueDate;
    }

    public function getStatus(): LoanStatus
    {
        return $this->status;
    }

    public function getPaidInterest(): MoneyVO
    {
        return $this->paidInterest;
    }

    public function getPaidCapital(): MoneyVO
    {
        return $this->paidCapital;
    }

    public function getNextPaymentDate(): DateVO
    {
        return $this->nextPaymentDate;
    }

    public function getCreatedAt(): DateVO
    {
        return $this->createdAt;
    }
}
