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
        private readonly UpdateCustomerUseCase $updateUseCase,
        private readonly AuditLogger $auditLogger
    ) {}

    public function store(Request $request): JsonResponse
    {
        $command = CreateCustomerRequest::fromArray($request->all());
        $response = $this->createUseCase->execute($command);

        $this->auditLogger->created('customer', $response['id'], [
            'name' => $response['first_name'].' '.$response['last_name'],
        ]);

        return response()->json($response, 201);
    }

    public function show(string $id): JsonResponse
    {
        $response = $this->getByIdUseCase->execute($id);

        return response()->json($response);
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

    public function update(Request $request, string $id): JsonResponse
    {
        $data = $request->all();
        $data['id'] = $id;
        $command = UpdateCustomerRequest::fromArray($data);
        $response = $this->updateUseCase->execute($command);

        $this->auditLogger->updated('customer', $id, $data);

        return response()->json($response);
    }
}
