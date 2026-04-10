<?php

declare(strict_types=1);

namespace App\LoanBC\Infrastructure\Persistence;

use App\CustomerBC\Domain\ValueObjects\CustomerIdVO;
use App\LoanBC\Domain\Entities\Loan;
use App\LoanBC\Domain\ValueObjects\InterestRateVO;
use App\LoanBC\Domain\ValueObjects\LoanIdVO;
use App\LoanBC\Domain\ValueObjects\LoanStatus;
use App\LoanBC\Infrastructure\Models\LoanModel;
use App\SharedKernel\Domain\ValueObjects\DateVO;
use App\SharedKernel\Domain\ValueObjects\MoneyVO;

final class LoanMapper
{
    public function toDomain(LoanModel $model): Loan
    {
        $paidInterest = $model->paid_interest > 0
            ? MoneyVO::create((int) $model->paid_interest)
            : MoneyVO::zero();
        $paidCapital = $model->paid_capital > 0
            ? MoneyVO::create((int) $model->paid_capital)
            : MoneyVO::zero();

        return Loan::reconstitute(
            LoanIdVO::fromString($model->id),
            CustomerIdVO::fromString($model->customer_id),
            MoneyVO::create((int) $model->original_capital),
            InterestRateVO::createAnnual((float) $model->interest_rate),
            DateVO::create($model->start_date),
            DateVO::create($model->due_date),
            DateVO::create($model->created_at),
            LoanStatus::from($model->status),
            $paidInterest,
            $paidCapital,
            MoneyVO::create((int) $model->capital),
            MoneyVO::create((int) $model->remaining_debt),
            DateVO::create($model->next_payment_date)
        );
    }

    public function toPersistence(Loan $loan): array
    {
        return [
            'id' => $loan->getId()->getValue(),
            'customer_id' => $loan->getCustomerId()->getValue(),
            'original_capital' => $loan->getOriginalCapital()->getAmount(),
            'capital' => $loan->getCapital()->getAmount(),
            'remaining_debt' => $loan->getRemainingDebt()->getAmount(),
            'paid_capital' => $loan->getPaidCapital()->getAmount(),
            'paid_interest' => $loan->getPaidInterest()->getAmount(),
            'interest_rate' => $loan->getInterestRate()->getAnnualRate(),
            'start_date' => $loan->getStartDate()->getFormatted(),
            'due_date' => $loan->getDueDate()->getFormatted(),
            'next_payment_date' => $loan->getNextPaymentDate()->getFormatted(),
            'status' => $loan->getStatus()->value,
        ];
    }
}
