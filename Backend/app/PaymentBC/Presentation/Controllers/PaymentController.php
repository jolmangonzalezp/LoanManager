<?php

declare(strict_types=1);

namespace App\PaymentBC\Presentation\Controllers;

use App\LoanBC\Domain\ValueObjects\LoanIdVO;
use App\PaymentBC\Application\Commands\ProcessPaymentCommand;
use App\PaymentBC\Application\UseCases\ProcessPaymentUseCase;
use App\SharedKernel\Domain\ValueObjects\MoneyVO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class PaymentController
{
    public function __construct(
        private readonly ProcessPaymentUseCase $processPaymentUseCase
    ) {}

    public function store(Request $request): JsonResponse
    {
        $data = $request->all();
        $amountCents = (int) (($data['amount'] ?? 0) * 100);
        $amount = MoneyVO::create($amountCents);
        $loanId = LoanIdVO::fromString($data['loan_id']);

        $command = new ProcessPaymentCommand(
            $loanId,
            $amount,
            $data['payment_date'] ?? null
        );

        $response = $this->processPaymentUseCase->execute($command);

        return response()->json($response->toArray(), 201);
    }
}
