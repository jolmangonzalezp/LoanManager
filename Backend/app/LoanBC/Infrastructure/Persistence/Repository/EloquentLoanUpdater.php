<?php

declare(strict_types=1);

namespace App\LoanBC\Infrastructure\Persistence\Repository;

use App\LoanBC\Domain\Aggregate\Loan;
use App\LoanBC\Domain\Repository\LoanUpdater;
use App\LoanBC\Infrastructure\Persistence\Model\LoanModel;

final class EloquentLoanUpdater implements LoanUpdater
{
    public function update(Loan $loan): void
    {
        $model = LoanModel::where('id', $loan->getId()->getValue())->first();

        if ($model === null) {
            return;
        }

        $model->remaining_debt = $loan->getRemainingDebt()->getAmount();
        $model->paid_capital = $loan->getPaidCapital()->getAmount();
        $model->paid_interest = $loan->getPaidInterest()->getAmount();
        $model->pending_interest = $loan->getPendingInterest()->getAmount();
        $model->interest_period = $loan->getInterestPeriod();
        $model->next_payment_date = $loan->getNextPaymentDate()->getFormatted();
        $model->status = $loan->getStatus()->value;

        $model->save();
    }
}