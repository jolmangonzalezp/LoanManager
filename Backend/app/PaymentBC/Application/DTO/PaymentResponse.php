<?php

declare(strict_types=1);

namespace App\PaymentBC\Application\DTO;

use App\PaymentBC\Domain\Aggregate\Payment;

final class PaymentResponse
{
    public ?string $customerName = null;

    public function __construct(
        public readonly string $id,
        public readonly string $loanId,
        public readonly int $amount,
        public readonly string $paymentDate,
        public readonly string $status,
        public readonly ?int $interestPaid,
        public readonly ?int $capitalPaid,
        public readonly string $createdAt
    ) {}

    public static function fromEntity(Payment $payment): self
    {
        return new self(
            id: $payment->getId()->getValue(),
            loanId: $payment->getLoanId()->getValue(),
            amount: $payment->getAmount()->getAmount(),
            paymentDate: $payment->getPaymentDate()->getFormatted(),
            status: $payment->getStatus()->value,
            interestPaid: $payment->getInterestPaid()?->getAmount(),
            capitalPaid: $payment->getCapitalPaid()?->getAmount(),
            createdAt: $payment->getCreatedAt()->getFormatted('Y-m-d H:i:s')
        );
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
        ];
    }
}