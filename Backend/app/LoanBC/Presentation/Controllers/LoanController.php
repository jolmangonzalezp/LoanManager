<?php

declare(strict_types=1);

namespace App\LoanBC\Presentation\Controllers;

use App\CustomerBC\Domain\ValueObjects\CustomerIdVO;
use App\LoanBC\Application\Commands\CreateLoanCommand;
use App\LoanBC\Application\Commands\MakePaymentCommand;
use App\LoanBC\Application\UseCases\CreateLoanUseCase;
use App\LoanBC\Application\UseCases\GetAllLoansUseCase;
use App\LoanBC\Application\UseCases\GetLoanByIdUseCase;
use App\LoanBC\Application\UseCases\GetLoanReportUseCase;
use App\LoanBC\Application\UseCases\MakePaymentUseCase;
use App\LoanBC\Domain\ValueObjects\InterestRateVO;
use App\LoanBC\Domain\ValueObjects\LoanIdVO;
use App\SharedKernel\Domain\ValueObjects\DateVO;
use App\SharedKernel\Domain\ValueObjects\MoneyVO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class LoanController
{
    public function __construct(
        private readonly CreateLoanUseCase $createLoanUseCase,
        private readonly GetLoanByIdUseCase $getLoanByIdUseCase,
        private readonly GetAllLoansUseCase $getAllLoansUseCase,
        private readonly GetLoanReportUseCase $getLoanReportUseCase,
        private readonly MakePaymentUseCase $makePaymentUseCase
    ) {}

    public function store(Request $request): JsonResponse
    {
        $data = $request->all();
        $amountCents = (int) (($data['capital'] ?? 0) * 100);
        $capital = MoneyVO::create($amountCents);
        $interestRate = InterestRateVO::createAnnual(
            (float) ($data['interest_rate'] ?? 0)
        );
        $startDate = DateVO::create($data['start_date'] ?? date('Y-m-d'));
        $dueDate = DateVO::create($data['due_date']);
        $customerId = CustomerIdVO::fromString($data['customer_id']);

        $command = new CreateLoanCommand(
            $customerId,
            $capital,
            $interestRate,
            $startDate,
            $dueDate
        );

        $response = $this->createLoanUseCase->execute($command);

        return response()->json($response->toArray(), 201);
    }

    public function show(string $id): JsonResponse
    {
        $response = $this->getLoanByIdUseCase->execute($id);

        return response()->json($response->toArray());
    }

    public function index(): JsonResponse
    {
        $responses = $this->getAllLoansUseCase->execute();

        return response()->json(array_map(
            fn ($response) => $response->toArray(),
            $responses
        ));
    }

    public function report(): JsonResponse
    {
        $response = $this->getLoanReportUseCase->execute();

        return response()->json($response->toArray());
    }

    public function makePayment(Request $request, string $id): JsonResponse
    {
        $data = $request->all();
        $amountCents = (int) (($data['amount'] ?? 0) * 100);
        $amount = MoneyVO::create($amountCents);

        $loanId = LoanIdVO::fromString($id);
        $command = new MakePaymentCommand($loanId, $amount);

        $response = $this->makePaymentUseCase->execute($command);

        return response()->json($response->toArray());
    }
}
