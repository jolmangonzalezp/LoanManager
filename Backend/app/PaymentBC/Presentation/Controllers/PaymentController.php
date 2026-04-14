<?php

declare(strict_types=1);

namespace App\PaymentBC\Presentation\Controllers;

use App\LoanBC\Domain\ValueObjects\LoanIdVO;
use App\PaymentBC\Application\Commands\ProcessPaymentCommand;
use App\PaymentBC\Application\UseCases\ProcessPaymentUseCase;
use App\PaymentBC\Infrastructure\Models\PaymentModel;
use App\PaymentBC\Application\DTOs\PaymentResponse;
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

    public function index(): JsonResponse
    {
        $payments = PaymentModel::with('loan.customer')->orderBy('created_at', 'desc')->get();
        
        $data = $payments->map(function ($p) {
            return [
                'id' => $p->id,
                'loan_id' => $p->loan_id,
                'amount' => $p->amount / 100,
                'payment_date' => $p->payment_date,
                'interest_paid' => $p->interest_paid / 100,
                'capital_paid' => $p->capital_paid / 100,
                'status' => $p->status,
                'created_at' => $p->created_at,
                'loan' => $p->loan ? [
                    'id' => $p->loan->id,
                    'capital' => ['amount' => $p->loan->capital],
                    'customer' => $p->loan->customer ? [
                        'name' => [
                            'first_name' => $p->loan->customer->first_name,
                            'last_name' => $p->loan->customer->last_name
                        ]
                    ] : null
                ] : null
            ];
        });

        return response()->json($data);
    }
}
