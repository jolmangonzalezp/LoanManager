<?php

declare(strict_types=1);

namespace App\PaymentBC\Infrastructure\Repositories;

use App\LoanBC\Domain\ValueObjects\LoanIdVO;
use App\PaymentBC\Domain\Entities\Payment;
use App\PaymentBC\Domain\ValueObjects\PaymentIdVO;
use App\PaymentBC\Domain\ValueObjects\PaymentStatus;
use App\PaymentBC\Infrastructure\Models\PaymentModel;
use App\SharedKernel\Domain\ValueObjects\DateVO;
use App\SharedKernel\Domain\ValueObjects\MoneyVO;

final class PaymentMapper
{
    public function toDomain(PaymentModel $model): Payment
    {
        $interestPaid = $model->interest_paid > 0
            ? MoneyVO::create((int) $model->interest_paid)
            : null;
        $capitalPaid = $model->capital_paid > 0
            ? MoneyVO::create((int) $model->capital_paid)
            : null;

        return Payment::reconstitute(
            PaymentIdVO::fromString($model->id),
            LoanIdVO::fromString($model->loan_id),
            MoneyVO::create((int) $model->amount),
            DateVO::create($model->payment_date),
            DateVO::create($model->created_at),
            PaymentStatus::from($model->status),
            $interestPaid,
            $capitalPaid
        );
    }

    public function toPersistence(Payment $payment): array
    {
        return [
            'id' => $payment->getId()->getValue(),
            'loan_id' => $payment->getLoanId()->getValue(),
            'amount' => $payment->getAmount()->getAmount(),
            'payment_date' => $payment->getPaymentDate()->getFormatted(),
            'interest_paid' => $payment->getInterestPaid()?->getAmount() ?? 0,
            'capital_paid' => $payment->getCapitalPaid()?->getAmount() ?? 0,
            'status' => $payment->getStatus()->value,
        ];
    }
}
