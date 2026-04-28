<?php

declare(strict_types=1);

namespace App\LoanBC\Application\DTO;

use App\LoanBC\Domain\Aggregate\Loan;

final class LoanResponse
{
    private string $loanNumber = '';
    private array $customerName = [];

    public function __construct(
        public readonly string $id,
        public readonly int $originalCapital,
        public readonly int $remainingDebt,
        public readonly float $interestRate,
        public readonly string $startDate,
        public readonly string $dueDate,
        public readonly ?string $nextPaymentDate,
        public readonly string $status,
        public readonly string $createdAt,
        public readonly string $customerId = '',
        public readonly int $paidInterest = 0,
        public readonly int $paidCapital = 0,
        public readonly int $pendingInterest = 0,
        public readonly string $interestPeriod = ''
    ) {
        $this->customerName = [
            'first_name' => '',
            'middle_name' => '',
            'last_name' => '',
            'second_last_name' => '',
        ];
    }

    public static function fromEntity(Loan $loan): self
    {
        return new self(
            id: $loan->getId()->getValue(),
            originalCapital: $loan->getOriginalCapital()->getAmount(),
            remainingDebt: $loan->getRemainingDebt()->getAmount(),
            interestRate: $loan->getInterestRate()->getMonthlyRate(),
            startDate: $loan->getStartDate()->getFormatted(),
            dueDate: $loan->getDueDate()->getFormatted(),
            nextPaymentDate: $loan->getNextPaymentDate()->getFormatted(),
            status: $loan->getStatus()->value,
            createdAt: $loan->getCreatedAt()->getFormatted('Y-m-d'),
            customerId: $loan->getCustomerId()->getValue(),
            paidInterest: $loan->getPaidInterest()->getAmount(),
            paidCapital: $loan->getPaidCapital()->getAmount(),
            pendingInterest: $loan->getPendingInterest()->getAmount(),
            interestPeriod: $loan->getInterestPeriod()
        );
    }

    public function setLoanNumber(string $loanNumber): void
    {
        $this->loanNumber = $loanNumber;
    }

    public function getLoanNumber(): string
    {
        return $this->loanNumber;
    }

    public function getCustomerId(): string
    {
        return $this->customerId;
    }

    public function setCustomerName(string $fullName): void
    {
        $parts = array_filter(explode(' ', trim($fullName)));
        $parts = array_values($parts);
        $count = count($parts);

        if ($count === 0) {
            return;
        }
        if ($count === 1) {
            $this->customerName['first_name'] = $parts[0];
            return;
        }
        if ($count === 2) {
            $this->customerName['first_name'] = $parts[0];
            $this->customerName['last_name'] = $parts[1];
            return;
        }
        if ($count === 3) {
            $this->customerName['first_name'] = $parts[0];
            $this->customerName['last_name'] = $parts[1];
            $this->customerName['second_last_name'] = $parts[2];
            return;
        }

        $this->customerName['first_name'] = $parts[0];
        $this->customerName['middle_name'] = $parts[1];
        $this->customerName['last_name'] = $parts[$count - 2];
        $this->customerName['second_last_name'] = $parts[$count - 1];
    }

    public function toArray(string $customerId): array
    {
        return [
            'id' => $this->id,
            'loan_number' => $this->loanNumber ?: null,
            'customer' => [
                'id' => $customerId,
                'name' => $this->customerName,
            ],
            'capital' => $this->originalCapital,
            'remaining_debt' => $this->remainingDebt,
            'interest_rate' => $this->interestRate,
            'start_date' => $this->startDate,
            'due_date' => $this->dueDate,
            'next_payment_date' => $this->nextPaymentDate,
            'status' => $this->status,
            'created_at' => $this->createdAt,
            'paid_interest' => $this->paidInterest,
            'paid_capital' => $this->paidCapital,
            'pending_interest' => $this->pendingInterest,
            'interest_period' => $this->interestPeriod,
        ];
    }
}
