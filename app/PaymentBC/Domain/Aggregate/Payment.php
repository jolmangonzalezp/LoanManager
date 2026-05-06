<?php

declare(strict_types=1);

namespace App\PaymentBC\Domain\Aggregate;

use App\LoanBC\Domain\ValueObject\LoanIdVO;
use App\PaymentBC\Domain\ValueObject\PaymentIdVO;
use App\PaymentBC\Domain\ValueObject\PaymentStatus;
use App\SharedKernel\Domain\Exception\DomainException;
use App\SharedKernel\Domain\ValueObject\DateVO;
use App\SharedKernel\Domain\ValueObject\MoneyVO;

final class Payment
{
    private PaymentStatus $status;

    private function __construct(
        private readonly PaymentIdVO $id,
        private readonly LoanIdVO $loanId,
        private readonly MoneyVO $amount,
        private readonly DateVO $paymentDate,
        private readonly DateVO $createdAt,
        private ?MoneyVO $interestPaid = null,
        private ?MoneyVO $capitalPaid = null,
        PaymentStatus $status = PaymentStatus::PENDING
    ) {
        $this->status = $status;
    }

    public static function create(
        LoanIdVO $loanId,
        MoneyVO $amount,
        ?DateVO $paymentDate = null
    ): self {
        $amountValue = $amount->getAmount();
        if ($amountValue <= 0) {
            throw new DomainException('invalid_amount', 'El monto debe ser mayor a 0');
        }

        return new self(
            PaymentIdVO::generate(),
            $loanId,
            $amount,
            $paymentDate ?? DateVO::now(),
            DateVO::now(),
            null,
            null,
            PaymentStatus::PENDING
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
        ?MoneyVO $capitalPaid
    ): self {
        return new self(
            $id,
            $loanId,
            $amount,
            $paymentDate,
            $createdAt,
            $interestPaid,
            $capitalPaid,
            $status
        );
    }

    public function validate(): self
    {
        if ($this->status !== PaymentStatus::PENDING) {
            throw new DomainException('invalid_status', 'El pago debe estar en estado pendiente');
        }

        $this->status = PaymentStatus::VALIDATED;

        return $this;
    }

    public function apply(
        MoneyVO $interestPortion,
        MoneyVO $capitalPortion
    ): self {
        if ($this->status !== PaymentStatus::VALIDATED) {
            throw new DomainException('not_validated', 'El pago debe estar validado antes de aplicar');
        }

        $appliedInterest = $interestPortion->getAmount() > 0 ? $interestPortion : MoneyVO::zero();
        $appliedCapital = $capitalPortion->getAmount() > 0 ? $capitalPortion : MoneyVO::zero();

        $this->interestPaid = $appliedInterest;
        $this->capitalPaid = $appliedCapital;
        $this->status = PaymentStatus::APPLIED;

        return $this;
    }

    public function reject(string $reason = ''): self
    {
        if ($this->status !== PaymentStatus::PENDING) {
            throw new DomainException('invalid_status', 'No se puede rechazar un pago ya procesado');
        }

        $this->status = PaymentStatus::REJECTED;

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
}
