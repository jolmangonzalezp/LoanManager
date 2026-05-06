<?php

declare(strict_types=1);

namespace App\PaymentBC\Application\DTO;

use App\PaymentBC\Domain\Aggregate\Payment;

final class PaymentResponse
{
    private ?int $remainingDebt = null;

    public function __construct(
        public readonly string $id,
        public readonly string $loanId,
        public readonly int $amount,
        public readonly string $paymentDate,
        public readonly string $status,
        public readonly ?int $interestPaid,
        public readonly int $capitalPaid,
        public readonly string $createdAt,
        public ?string $customerName = null
    ) {}

    public static function fromEntity(Payment $payment): self
    {
        return new self(
            id: $payment->getId()->getValue(),
            loanId: $payment->getLoanId()->getValue(),
            amount: $payment->getAmount()->getAmount(),
            paymentDate: $payment->getPaymentDate()->getFormatted(),
            status: $payment->getStatus()->value,
            interestPaid: $payment->getInterestPaid()?->getAmount() ?? 0,
            capitalPaid: $payment->getCapitalPaid()?->getAmount() ?? 0,
            createdAt: $payment->getCreatedAt()->getFormatted('Y-m-d H:i:s')
        );
    }

    public function withRemainingDebt(int $remainingDebt): self
    {
        $clone = clone $this;
        $clone->remainingDebt = $remainingDebt;
        return $clone;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'loan_id' => $this->loanId,
            'amount' => $this->amount,
            'payment_date' => $this->paymentDate,
            'status' => $this->status,
            'interest_paid' => $this->interestPaid,
            'capital_paid' => $this->capitalPaid,
            'created_at' => $this->createdAt,
            'customer_name' => $this->customerName,
            'remaining_debt' => $this->remainingDebt,
        ];
    }
}