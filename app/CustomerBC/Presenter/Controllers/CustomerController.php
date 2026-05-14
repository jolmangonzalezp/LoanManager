<?php

declare(strict_types=1);

namespace App\CustomerBC\Presenter\Controllers;

use App\CustomerBC\Application\UseCase\CreateCustomerUseCase;
use App\CustomerBC\Application\UseCase\GetAllCustomersSummaryUseCase;
use App\CustomerBC\Application\UseCase\GetAllCustomersUseCase;
use App\CustomerBC\Application\UseCase\GetCustomerByIdUseCase;
use App\CustomerBC\Application\UseCase\UpdateCustomerUseCase;
use App\CustomerBC\Infrastructure\Request\CreateCustomerRequest;
use App\CustomerBC\Infrastructure\Request\UpdateCustomerRequest;
use App\LoanBC\Application\UseCase\GetLoansByCustomerUseCase;
use App\SharedKernel\Application\Services\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class CustomerController
{
    public function __construct(
        private readonly CreateCustomerUseCase $createUseCase,
        private readonly GetCustomerByIdUseCase $getByIdUseCase,
        private readonly GetAllCustomersUseCase $getAllUseCase,
        private readonly GetAllCustomersSummaryUseCase $getAllSummaryUseCase,
        private readonly GetLoansByCustomerUseCase $getLoansByCustomerUseCase,
        private readonly UpdateCustomerUseCase $updateUseCase,
        private readonly AuditLogger $auditLogger
    ) {}

    public function store(Request $request): JsonResponse
    {
        $command = CreateCustomerRequest::fromArray($request);

        $success = $this->createUseCase->execute($command);

        $this->auditLogger->created('customer', $this->createUseCase->getResponse()->id, [
            'name' => $request->input('first_name').' '.$request->input('last_name'),
        ]);

        return response()->json($success, 201);
    }

    public function show(string $id): JsonResponse
    {
        $response = $this->getByIdUseCase->execute($id);

        return response()->json($response);
    }

    public function loans(string $id): JsonResponse
    {
        $loans = $this->getLoansByCustomerUseCase->execute($id);

        return response()->json($loans);
    }

    public function index(): JsonResponse
    {
        $responses = $this->getAllUseCase->execute();

        return response()->json($responses);
    }

    public function summary(): JsonResponse
    {
        $responses = $this->getAllSummaryUseCase->execute();

        return response()->json($responses);
    }

    public function unassigned(): JsonResponse
    {
        $customers = \App\CustomerBC\Infrastructure\Persistence\Model\CustomerModel::whereNull('zone_id')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get(['id', 'first_name', 'last_name', 'latitude', 'longitude']);

        return response()->json($customers);
    }

    public function report(): JsonResponse
    {
        $responses = $this->getAllUseCase->execute();

        $totalCustomers = count($responses);
        $customersWithLoans = 0;
        $totalDebt = 0;

        foreach ($responses as $customer) {
            $loans = $this->getLoansByCustomerUseCase->execute($customer['id']);
            if (! empty($loans)) {
                $customersWithLoans++;
                foreach ($loans as $loan) {
                    $totalDebt += $loan['balance'] ?? 0;
                }
            }
        }

        return response()->json([
            'total_customers' => $totalCustomers,
            'customers_with_loans' => $customersWithLoans,
            'customers_without_loans' => $totalCustomers - $customersWithLoans,
            'total_debt' => $totalDebt,
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $command = UpdateCustomerRequest::fromArray($id, $request);
        $success = $this->updateUseCase->execute($command);

        $this->auditLogger->updated('customer', $id, $request->toArray());

        return response()->json($success);
    }
}
