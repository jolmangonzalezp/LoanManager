<?php

declare(strict_types=1);

namespace App\PaymentBC\Infrastructure\Request;

use App\PaymentBC\Application\CQRS\Command\UpdatePaymentCommand;
use App\PaymentBC\Domain\ValueObject\LoanIdVO;
use App\PaymentBC\Domain\ValueObject\PaymentIdVO;
use App\PaymentBC\Domain\ValueObject\PaymentMethod;
use App\SharedKernel\Domain\ValueObject\DateVO;
use App\SharedKernel\Domain\ValueObject\MoneyVO;
use Illuminate\Http\Request;

final class UpdatePaymentRequest
{
    public static function toCommand(string $id, Request $request): UpdatePaymentCommand
    {
        $request->validate([
            'loan_id' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'payment_date' => 'sometimes|date',
            'payment_method' => 'sometimes|string|in:cash,bank_transfer,card,check,other',
        ]);

        $paymentDate = $request->input('payment_date')
            ? DateVO::fromString($request->input('payment_date'))
            : null;

        return new UpdatePaymentCommand(
            paymentId: PaymentIdVO::fromString($id),
            loanId: LoanIdVO::fromString($request->input('loan_id')),
            amount: MoneyVO::create((int) $request->input('amount')),
            paymentDate: $paymentDate,
            paymentMethod: $request->input('payment_method')
                ? PaymentMethod::from($request->input('payment_method'))
                : PaymentMethod::CASH,
        );
    }
}
