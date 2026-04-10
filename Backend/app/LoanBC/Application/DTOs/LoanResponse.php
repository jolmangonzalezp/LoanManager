<?php

declare(strict_types=1);

namespace App\LoanBC\Application\DTOs;

use App\LoanBC\Domain\Entities\Loan;
use App\SharedKernel\Domain\ValueObjects\MoneyVO;

final class LoanResponse
{
    public function __construct(
        public readonly string $id,
        public readonly string $customerId,
        public readonly array $capital,
        public readonly array $remainingDebt,
        public readonly array $interestRate,
        public readonly string $startDate,
        public readonly string $dueDate,
        public readonly string $nextPaymentDate,
        public readonly string $status,
        public readonly array $paidCapital,
        public readonly array $paidInterest,
        public readonly string $createdAt
    ) {}

    private static function moneyToArray(MoneyVO $money): array
    {
        return [
            'amount' => $money->getAmount(),
            'currency' => $money->getCurrency()->value,
        ];
    }

    public static function fromEntity(Loan $loan): self
    {
        return new self(
            id: $loan->getId()->getValue(),
            customerId: $loan->getCustomerId()->getValue(),
            capital: self::moneyToArray($loan->getCapital()),
            remainingDebt: self::moneyToArray($loan->getRemainingDebt()),
            interestRate: [
                'annual' => $loan->getInterestRate()->getAnnualRate(),
                'monthly' => $loan->getInterestRate()->getMonthlyRate(),
            ],
            startDate: $loan->getStartDate()->getFormatted(),
            dueDate: $loan->getDueDate()->getFormatted(),
            nextPaymentDate: $loan->getNextPaymentDate()->getFormatted(),
            status: $loan->getStatus()->value,
            paidCapital: self::moneyToArray($loan->getPaidCapital()),
            paidInterest: self::moneyToArray($loan->getPaidInterest()),
            createdAt: $loan->getCreatedAt()->getFormatted('Y-m-d H:i:s')
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'customer_id' => $this->customerId,
            'capital' => $this->capital,
            'remaining_debt' => $this->remainingDebt,
            'interest_rate' => $this->interestRate,
            'start_date' => $this->startDate,
            'due_date' => $this->dueDate,
            'next_payment_date' => $this->nextPaymentDate,
            'status' => $this->status,
            'paid_capital' => $this->paidCapital,
            'paid_interest' => $this->paidInterest,
            'created_at' => $this->createdAt,
        ];
    }
}
