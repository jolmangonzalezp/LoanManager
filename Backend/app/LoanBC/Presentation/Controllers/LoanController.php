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
use Illuminate\Support\Facades\Log;

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
        
        $monthlyRate = (float) ($data['interest_rate'] ?? 0);
        $interestRate = InterestRateVO::createMonthly($monthlyRate);
        
        $startDateStr = $data['start_date'] ?? date('Y-m-d');
        $startDate = DateVO::create($startDateStr);
        
        $termMonths = $data['term'] ?? 24;
        $dueDate = date('Y-m-d', strtotime($startDateStr . ' + ' . $termMonths . ' months'));
        $dueDateVO = DateVO::create($dueDate);
        
        $customerId = CustomerIdVO::fromString($data['customer_id']);

        $command = new CreateLoanCommand(
            $customerId,
            $capital,
            $interestRate,
            $startDate,
            $dueDateVO
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
        try {
            $responses = $this->getAllLoansUseCase->execute();
            
            $responsesWithCustomer = array_map(function ($response) {
                $customerId = $response->customerId;
                $customerName = null;
                
                try {
                    $customerData = \Illuminate\Support\Facades\DB::table('customers')
                        ->where('id', $customerId)
                        ->first();
                        
                    if ($customerData) {
                        try {
                            $firstName = $customerData->first_name ? \Illuminate\Support\Facades\Crypt::decryptString($customerData->first_name) : '';
                            $lastName = $customerData->last_name ? \Illuminate\Support\Facades\Crypt::decryptString($customerData->last_name) : '';
                            $customerName = trim($firstName . ' ' . $lastName);
                        } catch (\Exception $e) {
                            $customerName = null;
                        }
                    }
                } catch (\Exception $e) {
                }
                
                $arr = $response->toArray();
                $arr['customer_name'] = $customerName;
                return $arr;
            }, $responses);

            return response()->json($responsesWithCustomer);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('LoanController index error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
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
