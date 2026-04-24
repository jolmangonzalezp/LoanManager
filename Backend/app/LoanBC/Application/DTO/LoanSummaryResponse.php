<?php

declare(strict_types=1);

namespace App\LoanBC\Application\DTO;

final class LoanSummaryResponse
{
    public function __construct(
        public readonly string $id,
        public readonly string $loanNumber,
        public readonly int $capital,
        public readonly int $remainingDebt,
        public readonly string $status,
        public readonly string $dueDate
    ) {}

    public static function fromEntity($loan, string $loanNumber): self
    {
        return new self(
            id: $loan->getId()->getValue(),
            loanNumber: $loanNumber,
            capital: $loan->getOriginalCapital()->getAmount(),
            remainingDebt: $loan->getRemainingDebt()->getAmount(),
            status: $loan->getStatus()->value,
            dueDate: $loan->getDueDate()->getFormatted()
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'loan_number' => $this->loanNumber,
            'balance' => $this->remainingDebt,
            'status' => $this->status,
            'due_date' => $this->dueDate,
        ];
    }
}