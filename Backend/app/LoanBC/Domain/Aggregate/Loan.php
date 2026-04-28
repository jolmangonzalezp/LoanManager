<?php

declare(strict_types=1);

namespace App\LoanBC\Domain\Aggregate;

use App\CustomerBC\Domain\ValueObject\CustomerIdVO;
use App\LoanBC\Domain\DTO\PaymentDistributionResult;
use App\LoanBC\Domain\ValueObject\InterestRateVO;
use App\LoanBC\Domain\ValueObject\LoanIdVO;
use App\LoanBC\Domain\ValueObject\LoanStatus;
use App\SharedKernel\Domain\Exception\DomainException;
use App\SharedKernel\Domain\ValueObject\DateVO;
use App\SharedKernel\Domain\ValueObject\MoneyVO;

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
        private MoneyVO $remainingDebt,
        private MoneyVO $pendingInterest,
        private string $interestPeriod,
        private DateVO $nextPaymentDate
    ) {}

    public static function create(
        CustomerIdVO $customerId,
        MoneyVO $capital,
        InterestRateVO $interestRate,
        DateVO $startDate,
        DateVO $dueDate
    ): self {
        $monthlyInterest = $interestRate->calculateInterest($capital);
        $startDateVO = $startDate->isFuture() ? DateVO::now() : $startDate;
        $nextPaymentDate = $startDateVO->addMonths(1);
        $interestPeriod = $startDateVO->getFormatted('Y-m');

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
            $monthlyInterest,
            $interestPeriod,
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
        MoneyVO $remainingDebt,
        MoneyVO $pendingInterest,
        string $interestPeriod,
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
            $remainingDebt,
            $pendingInterest,
            $interestPeriod,
            $nextPaymentDate
        );
    }

    public function makePayment(MoneyVO $amount): PaymentDistributionResult
    {
        if ($this->status !== LoanStatus::ACTIVE) {
            throw new DomainException('inactive_loan', 'El préstamo no está activo');
        }

        $currentPeriod = DateVO::now()->getFormatted('Y-m');

        if ($currentPeriod === $this->interestPeriod) {
            if ($amount->getAmount() >= $this->pendingInterest->getAmount()) {
                $interestPortion = $this->pendingInterest;
                $remainingAfterInterest = $amount->subtract($this->pendingInterest);
                $capitalPortion = $remainingAfterInterest;
                $newPendingInterest = MoneyVO::zero();
            } else {
                $interestPortion = $amount;
                $capitalPortion = MoneyVO::zero();
                $newPendingInterest = $this->pendingInterest->subtract($amount);
            }
        } else {
            $newMonthlyInterest = $this->interestRate->calculateInterest($this->remainingDebt);

            if ($amount->getAmount() >= $newMonthlyInterest->getAmount()) {
                $interestPortion = $newMonthlyInterest;
                $remainingAfterInterest = $amount->subtract($newMonthlyInterest);
                $capitalPortion = $remainingAfterInterest;
                $newPendingInterest = MoneyVO::zero();
            } else {
                $interestPortion = $amount;
                $capitalPortion = MoneyVO::zero();
                $newPendingInterest = $newMonthlyInterest->subtract($amount);
            }
        }

        $newPaidInterest = $this->paidInterest->add($interestPortion);
        $newPaidCapital = $this->paidCapital->add($capitalPortion);
        $newRemainingDebt = $this->remainingDebt->subtract($capitalPortion);
        $newStatus = $newRemainingDebt->isZero() ? LoanStatus::PAID : LoanStatus::ACTIVE;

        $updatedLoan = new self(
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
            $newRemainingDebt,
            $newPendingInterest,
            $this->interestPeriod,
            $this->nextPaymentDate
        );

        return new PaymentDistributionResult($updatedLoan, $interestPortion, $capitalPortion);
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

    public function getPendingInterest(): MoneyVO
    {
        return $this->pendingInterest;
    }

    public function getInterestPeriod(): string
    {
        return $this->interestPeriod;
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