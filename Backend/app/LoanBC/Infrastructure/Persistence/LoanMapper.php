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
        $originalCapital = (int) $model->original_capital;
        $capital = (int) $model->capital;
        $remainingDebt = (int) $model->remaining_debt;
        $paidInterest = (int) $model->paid_interest;
        $paidCapital = (int) $model->paid_capital;

        $paidInterestVO = $paidInterest > 0
            ? MoneyVO::create($paidInterest)
            : MoneyVO::zero();
        $paidCapitalVO = $paidCapital > 0
            ? MoneyVO::create($paidCapital)
            : MoneyVO::zero();
        $originalCapitalVO = $originalCapital > 0
            ? MoneyVO::create($originalCapital)
            : MoneyVO::zero();
        $capitalVO = $capital > 0
            ? MoneyVO::create($capital)
            : MoneyVO::zero();
        $remainingDebtVO = $remainingDebt > 0
            ? MoneyVO::create($remainingDebt)
            : MoneyVO::zero();

        return Loan::reconstitute(
            LoanIdVO::fromString($model->id),
            CustomerIdVO::fromString($model->customer_id),
            $originalCapitalVO,
            InterestRateVO::createAnnual((float) $model->interest_rate),
            DateVO::create($model->start_date),
            DateVO::create($model->due_date),
            DateVO::create($model->created_at),
            LoanStatus::from($model->status),
            $paidInterestVO,
            $paidCapitalVO,
            $capitalVO,
            $remainingDebtVO,
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
