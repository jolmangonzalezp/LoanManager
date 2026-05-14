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

final readonly class LoanController
{
    public function __construct(
        private CreateLoanUseCase    $createLoanUseCase,
        private GetLoanByIdUseCase   $getLoanByIdUseCase,
        private GetAllLoansUseCase   $getAllLoansUseCase,
        private GetLoanReportUseCase $getLoanReportUseCase,
        private MakePaymentUseCase   $makePaymentUseCase,
        private UpdateLoanUseCase    $updateLoanUseCase,
        private AuditLogger          $auditLogger
    ) {}

    public function store(Request $request): JsonResponse
    {
        $command = CreateLoanRequest::fromArray($request);

        $success = $this->createLoanUseCase->execute($command);

        return response()->json($success ? $this->createLoanUseCase->getResponse() : null, 201);
    }

    public function show(string $id): JsonResponse
    {
        $response = $this->getLoanByIdUseCase->execute($id);

        return response()->json($response);
    }

    public function index(): JsonResponse
    {
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
        $this->makePaymentUseCase->execute($command);
        $response = $this->makePaymentUseCase->getResponse();

        $this->auditLogger->payment($id, [
            'amount' => $request->input('amount'),
            'payment_date' => $request->input('payment_date'),
            'new_remaining_debt' => $response['remainingDebt'],
        ]);

        return response()->json($response);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $response = $this->updateLoanUseCase->execute($id, $request->all());;

        $this->auditLogger->updated('loan', $id, $request->all());

        return response()->json($response);
    }
}
