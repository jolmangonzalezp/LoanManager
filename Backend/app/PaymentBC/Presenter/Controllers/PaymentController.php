<?php

declare(strict_types=1);

namespace App\PaymentBC\Presenter\Controllers;

use App\LoanBC\Domain\ValueObject\LoanIdVO;
use App\PaymentBC\Application\CQRS\Command\ProcessPaymentCommand;
use App\PaymentBC\Application\UseCase\GetAllPaymentsUseCase;
use App\PaymentBC\Application\UseCase\GetPaymentByIdUseCase;
use App\PaymentBC\Application\UseCase\ProcessPaymentUseCase;
use App\SharedKernel\Domain\ValueObject\MoneyVO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class PaymentController
{
    public function __construct(
        private readonly ProcessPaymentUseCase $processPaymentUseCase,
        private readonly GetAllPaymentsUseCase $getAllPaymentsUseCase,
        private readonly GetPaymentByIdUseCase $getPaymentByIdUseCase
    ) {}

    public function store(Request $request): JsonResponse
    {
        $data = $request->all();
        $amount = MoneyVO::create((int) ($data['amount'] ?? 0));
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
        $responses = $this->getAllPaymentsUseCase->execute();

        return response()->json($responses);
    }

    public function show(string $id): JsonResponse
    {
        $response = $this->getPaymentByIdUseCase->execute($id);

        return response()->json($response);
    }
}
