<?php

declare(strict_types=1);

namespace App\PaymentBC\Infrastructure\Mapper;

use App\LoanBC\Domain\ValueObject\LoanIdVO;
use App\PaymentBC\Domain\Aggregate\Payment;
use App\PaymentBC\Domain\ValueObject\PaymentIdVO;
use App\PaymentBC\Domain\ValueObject\PaymentStatus;
use App\PaymentBC\Infrastructure\Persistence\Model\PaymentModel;
use App\SharedKernel\Domain\ValueObject\DateVO;
use App\SharedKernel\Domain\ValueObject\MoneyVO;

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
            DateVO::fromDateTime($model->payment_date),
            DateVO::fromDateTime($model->created_at),
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
            'created_at' => $payment->getCreatedAt()->getFormatted(),
        ];
    }
}
