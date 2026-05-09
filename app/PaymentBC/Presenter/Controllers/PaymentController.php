<?php

declare(strict_types=1);

namespace App\PaymentBC\Presenter\Controllers;

use App\PaymentBC\Domain\ValueObject\LoanIdVO;
use App\PaymentBC\Application\CQRS\Command\ProcessPaymentCommand;
use App\PaymentBC\Application\UseCase\GetAllPaymentsUseCase;
use App\PaymentBC\Application\UseCase\GetMonthlyReportUseCase;
use App\PaymentBC\Application\UseCase\GetPaymentByIdUseCase;
use App\PaymentBC\Application\UseCase\ProcessPaymentUseCase;
use App\PaymentBC\Application\UseCase\UpdatePaymentUseCase;
use App\PaymentBC\Infrastructure\Request\UpdatePaymentRequest;
use App\SharedKernel\Domain\ValueObject\DateVO;
use App\SharedKernel\Domain\ValueObject\MoneyVO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class PaymentController
{
    public function __construct(
        private readonly ProcessPaymentUseCase $processPaymentUseCase,
        private readonly GetAllPaymentsUseCase $getAllPaymentsUseCase,
        private readonly GetPaymentByIdUseCase $getPaymentByIdUseCase,
        private readonly GetMonthlyReportUseCase $getMonthlyReportUseCase,
        private readonly UpdatePaymentUseCase $updatePaymentUseCase
    ) {}

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'loan_id' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'payment_date' => 'sometimes|date',
        ]);

        $command = new ProcessPaymentCommand(
            LoanIdVO::fromString($data['loan_id']),
            MoneyVO::create((int) $data['amount']),
            isset($data['payment_date']) ? DateVO::fromString($data['payment_date']) : null
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

    public function monthly(): JsonResponse
    {
        $response = $this->getMonthlyReportUseCase->execute();

        return response()->json($response->toArray());
    }
    public function update(Request $request, string $id): JsonResponse
    {
        $command = UpdatePaymentRequest::toCommand($id, $request);

        $response = $this->updatePaymentUseCase->execute($command);

        return response()->json($response->toArray());
    }
}
