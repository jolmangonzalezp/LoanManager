<?php

declare(strict_types=1);

namespace App\LoanBC\Infrastructure\Mapper;

use App\CustomerBC\Domain\ValueObject\CustomerIdVO;
use App\LoanBC\Domain\Aggregate\Loan;
use App\LoanBC\Domain\ValueObject\InterestRateVO;
use App\LoanBC\Domain\ValueObject\LoanIdVO;
use App\LoanBC\Domain\ValueObject\LoanStatus;
use App\LoanBC\Infrastructure\Persistence\Model\LoanModel;
use App\SharedKernel\Domain\ValueObject\DateVO;
use App\SharedKernel\Domain\ValueObject\MoneyVO;

final class LoanMapper
{
    private ?string $currentLoanNumber = null;

    public function setCurrentLoanNumber(string $loanNumber): void
    {
        $this->currentLoanNumber = $loanNumber;
    }

    public function toDomain(LoanModel $model): Loan
    {
        $this->currentLoanNumber = $model->loan_number;

        $originalCapital = (int) $model->original_capital;
        $remainingDebt = (int) $model->remaining_debt;
        $paidInterest = (int) ($model->paid_interest ?? 0);
        $paidCapital = (int) ($model->paid_capital ?? 0);
        $pendingInterest = (int) ($model->pending_interest ?? 0);
        $interestPeriod = $model->interest_period ?? '';

        return Loan::reconstitute(
            LoanIdVO::fromString($model->id),
            CustomerIdVO::fromString($model->customer_id),
            $originalCapital > 0 ? MoneyVO::create($originalCapital) : MoneyVO::zero(),
            InterestRateVO::createMonthly((float) $model->interest_rate),
            DateVO::fromDateTime($model->start_date),
            DateVO::fromDateTime($model->due_date),
            DateVO::fromDateTime($model->created_at),
            LoanStatus::from($model->status),
            $paidInterest > 0 ? MoneyVO::create($paidInterest) : MoneyVO::zero(),
            $paidCapital > 0 ? MoneyVO::create($paidCapital) : MoneyVO::zero(),
            $remainingDebt > 0 ? MoneyVO::create($remainingDebt) : MoneyVO::zero(),
            $pendingInterest > 0 ? MoneyVO::create($pendingInterest) : MoneyVO::zero(),
            $interestPeriod,
            DateVO::fromDateTime($model->next_payment_date)
        );
    }

    public function toPersistence(Loan $loan): array
    {
        return [
            'id' => $loan->getId()->getValue(),
            'loan_number' => $this->currentLoanNumber,
            'customer_id' => $loan->getCustomerId()->getValue(),
            'original_capital' => $loan->getOriginalCapital()->getAmount(),
            'remaining_debt' => $loan->getRemainingDebt()->getAmount(),
            'paid_capital' => $loan->getPaidCapital()->getAmount(),
            'paid_interest' => $loan->getPaidInterest()->getAmount(),
            'pending_interest' => $loan->getPendingInterest()->getAmount(),
            'interest_period' => $loan->getInterestPeriod(),
            'interest_rate' => $loan->getInterestRate()->getMonthlyRate(),
            'start_date' => $loan->getStartDate()->getFormatted(),
            'due_date' => $loan->getDueDate()->getFormatted(),
            'next_payment_date' => $loan->getNextPaymentDate()->getFormatted(),
            'status' => $loan->getStatus()->value,
        ];
    }
}