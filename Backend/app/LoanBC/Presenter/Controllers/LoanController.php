<?php

declare(strict_types=1);

namespace App\LoanBC\Presenter\Controllers;

use App\LoanBC\Application\UseCase\CreateLoanUseCase;
use App\LoanBC\Application\UseCase\GetAllLoansUseCase;
use App\LoanBC\Application\UseCase\GetLoanByIdUseCase;
use App\LoanBC\Application\UseCase\GetLoanReportUseCase;
use App\LoanBC\Application\UseCase\MakePaymentUseCase;
use App\LoanBC\Application\UseCase\UpdateLoanUseCase;
use App\LoanBC\Infrastructure\Request\CreateLoanRequest;
use App\LoanBC\Infrastructure\Request\MakePaymentRequest;
use App\SharedKernel\Application\Services\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class LoanController
{
    public function __construct(
        private readonly CreateLoanUseCase $createLoanUseCase,
        private readonly GetLoanByIdUseCase $getLoanByIdUseCase,
        private readonly GetAllLoansUseCase $getAllLoansUseCase,
        private readonly GetLoanReportUseCase $getLoanReportUseCase,
        private readonly MakePaymentUseCase $makePaymentUseCase,
        private readonly UpdateLoanUseCase $updateLoanUseCase,
        private readonly AuditLogger $auditLogger
    ) {}

    public function store(Request $request): JsonResponse
    {
        $command = CreateLoanRequest::fromArray($request);
        $response = $this->createLoanUseCase->execute($command);

        $this->auditLogger->created('loan', $response->id, [
            'loan_number' => $response->getLoanNumber(),
            'customer_id' => $response->getCustomerId(),
            'capital' => $response->originalCapital,
        ]);

        return response()->json($response->toArray($response->getCustomerId()), 201);
    }

    public function show(string $id): JsonResponse
    {
        $response = $this->getLoanByIdUseCase->execute($id);

        return response()->json($response);
    }

    public function index(): JsonResponse
    {
        // TODO: La hidratación del nombre del cliente debe ocurrir en una capa de Query (CQRS)
        // Por ahora mantenemos la lista simple.
        $responses = $this->getAllLoansUseCase->execute();

        return response()->json($responses);
    }

    public function report(): JsonResponse
    {
        $response = $this->getLoanReportUseCase->execute();

        return response()->json($response->toArray());
    }

    public function makePayment(Request $request, string $id): JsonResponse
    {
        $command = MakePaymentRequest::fromArray($id, $request->all());
        $response = $this->makePaymentUseCase->execute($command);

        $this->auditLogger->payment($id, [
            'amount' => $request->input('amount'),
            'payment_date' => $request->input('payment_date'),
            'new_remaining_debt' => $response->remainingDebt,
        ]);

        return response()->json($response->toArray());
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $response = $this->updateLoanUseCase->execute($id, $request->all());

        $this->auditLogger->updated('loan', $id, $request->all());

        return response()->json($response->toArray($response->getCustomerId()));
    }
}
