<?php

declare(strict_types=1);

namespace App\PaymentBC\Domain\Aggregate;

use App\PaymentBC\Domain\ValueObject\LoanIdVO;
use App\PaymentBC\Domain\ValueObject\PaymentIdVO;
use App\PaymentBC\Domain\Exception\InvalidPaymentUpdateException;
use App\PaymentBC\Domain\ValueObject\PaymentMethod;
use App\PaymentBC\Domain\ValueObject\PaymentStatus;
use App\SharedKernel\Domain\ValueObject\DateVO;
use App\SharedKernel\Domain\ValueObject\MoneyVO;

final class Payment
{
    private PaymentStatus $status;

    private function __construct(
        private readonly PaymentIdVO $id,
        private LoanIdVO $loanId,
        private MoneyVO $amount,
        private DateVO $paymentDate,
        private readonly DateVO $createdAt,
        PaymentStatus $status = PaymentStatus::PENDING,
        private ?MoneyVO $interestPaid = null,
        private ?MoneyVO $capitalPaid = null,
        private PaymentMethod $paymentMethod = PaymentMethod::CASH,
    ) {
        $this->status = $status;
    }

    public static function create(
        LoanIdVO $loanId,
        MoneyVO $amount,
        ?DateVO $paymentDate,
        PaymentMethod $paymentMethod = PaymentMethod::CASH,
    ): self {
        return new self(
            PaymentIdVO::generate(),
            $loanId,
            $amount,
            $paymentDate,
            DateVO::now(),
            PaymentStatus::PENDING,
            null,
            null,
            $paymentMethod
        );
    }

    public static function reconstitute(
        PaymentIdVO $id,
        LoanIdVO $loanId,
        MoneyVO $amount,
        DateVO $paymentDate,
        DateVO $createdAt,
        PaymentStatus $status,
        ?MoneyVO $interestPaid,
        ?MoneyVO $capitalPaid,
        PaymentMethod $paymentMethod = PaymentMethod::CASH,
    ): self {
        return new self(
            $id,
            $loanId,
            $amount,
            $paymentDate,
            $createdAt,
            $status,
            $interestPaid,
            $capitalPaid,
            $paymentMethod
        );
    }

    public function update(LoanIdVO $loanId, MoneyVO $amount, ?DateVO $paymentDate = null, ?PaymentMethod $paymentMethod = null): void
    {
        if ($this->status === PaymentStatus::REJECTED || $this->status === PaymentStatus::REFUNDED) {
            throw new InvalidPaymentUpdateException('Cannot update a rejected or refunded payment');
        }

        $this->loanId = $loanId;
        $this->amount = $amount;
        if ($paymentDate !== null) {
            $this->paymentDate = $paymentDate;
        }
        if ($paymentMethod !== null) {
            $this->paymentMethod = $paymentMethod;
        }
        $this->status = PaymentStatus::PENDING;
        $this->interestPaid = null;
        $this->capitalPaid = null;
    }

    public function apply(
        MoneyVO $interestPortion,
        MoneyVO $capitalPortion
    ): self {

        $appliedInterest = $interestPortion ? $interestPortion : MoneyVO::zero();
        $appliedCapital = $capitalPortion->getAmount() > 0 ? $capitalPortion : MoneyVO::zero();

        $this->interestPaid = $appliedInterest;
        $this->capitalPaid = $appliedCapital;
        $this->status = PaymentStatus::APPLIED;

        return $this;
    }

    public function getId(): PaymentIdVO
    {
        return $this->id;
    }

    public function getLoanId(): LoanIdVO
    {
        return $this->loanId;
    }

    public function getAmount(): MoneyVO
    {
        return $this->amount;
    }

    public function getPaymentDate(): DateVO
    {
        return $this->paymentDate;
    }

    public function getCreatedAt(): DateVO
    {
        return $this->createdAt;
    }

    public function getStatus(): PaymentStatus
    {
        return $this->status;
    }

    public function getInterestPaid(): ?MoneyVO
    {
        return $this->interestPaid;
    }

    public function getCapitalPaid(): ?MoneyVO
    {
        return $this->capitalPaid;
    }

    public function getPaymentMethod(): PaymentMethod
    {
        return $this->paymentMethod;
    }
}
