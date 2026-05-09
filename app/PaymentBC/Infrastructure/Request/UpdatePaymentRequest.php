<?php

declare(strict_types=1);

namespace App\PaymentBC\Infrastructure\Request;

use App\PaymentBC\Application\CQRS\Command\UpdatePaymentCommand;
use App\PaymentBC\Domain\ValueObject\LoanIdVO;
use App\PaymentBC\Domain\ValueObject\PaymentIdVO;
use App\SharedKernel\Domain\ValueObject\DateVO;
use App\SharedKernel\Domain\ValueObject\MoneyVO;
use Illuminate\Http\Request;

final class UpdatePaymentRequest
{
    public static function toCommand(string $id, Request $request): UpdatePaymentCommand
    {
        $paymentDate = $request->input('payment_date')
            ? DateVO::fromString($request->input('payment_date'))
            : null;

        return new UpdatePaymentCommand(
            paymentId: PaymentIdVO::fromString($id),
            loanId: LoanIdVO::fromString($request->input('loan_id')),
            amount: MoneyVO::create((int) $request->input('amount')),
            paymentDate: $paymentDate
        );
    }
}
