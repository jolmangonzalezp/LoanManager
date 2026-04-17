<?php

declare(strict_types=1);

namespace App\PaymentBC\Application\DTO;

use App\PaymentBC\Domain\Aggregate\Payment;
use App\SharedKernel\Domain\ValueObject\MoneyVO;

final class PaymentResponse
{
    public ?string $customerName = null;

    public function __construct(
        public readonly string $id,
        public readonly string $loanId,
        public readonly array $amount,
        public readonly string $paymentDate,
        public readonly string $status,
        public readonly ?array $interestPaid,
        public readonly ?array $capitalPaid,
        public readonly string $createdAt
    ) {}

    public static function fromEntity(Payment $payment): self
    {
        return new self(
            id: $payment->getId()->getValue(),
            loanId: $payment->getLoanId()->getValue(),
            amount: self::moneyToArray($payment->getAmount()),
            paymentDate: $payment->getPaymentDate()->getFormatted(),
            status: $payment->getStatus()->value,
            interestPaid: $payment->getInterestPaid()
                ? self::moneyToArray($payment->getInterestPaid())
                : null,
            capitalPaid: $payment->getCapitalPaid()
                ? self::moneyToArray($payment->getCapitalPaid())
                : null,
            createdAt: $payment->getCreatedAt()->getFormatted('Y-m-d H:i:s')
        );
    }

    private static function moneyToArray(MoneyVO $money): array
    {
        return [
            'amount' => $money->getAmount(),
        ];
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
