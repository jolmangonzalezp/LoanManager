<?php

declare(strict_types=1);

namespace App\CustomerBC\Presentation\Controllers;

use App\CustomerBC\Application\DTOs\CustomerResponse;
use App\CustomerBC\Application\DTOs\CustomerSummaryResponse;
use App\CustomerBC\Application\UseCases\CreateCustomerUseCase;
use App\CustomerBC\Application\UseCases\GetAllCustomersSummaryUseCase;
use App\CustomerBC\Application\UseCases\GetAllCustomersUseCase;
use App\CustomerBC\Application\UseCases\GetCustomerByIdUseCase;
use App\CustomerBC\Application\UseCases\GetCustomerReportUseCase;
use App\CustomerBC\Application\UseCases\UpdateCustomerUseCase;
use App\CustomerBC\Presentation\Mappers\CreateCustomerRequestMapper;
use App\CustomerBC\Presentation\Mappers\UpdateCustomerRequestMapper;
use Illuminate\Http\JsonResponse;

final class CustomerController
{
    public function __construct(
        private readonly CreateCustomerRequestMapper $createMapper,
        private readonly UpdateCustomerRequestMapper $updateMapper,
        private readonly CreateCustomerUseCase $createUseCase,
        private readonly GetCustomerByIdUseCase $getByIdUseCase,
        private readonly GetAllCustomersUseCase $getAllUseCase,
        private readonly GetAllCustomersSummaryUseCase $getAllSummaryUseCase,
        private readonly UpdateCustomerUseCase $updateUseCase,
        private readonly GetCustomerReportUseCase $reportUseCase
    ) {}

    public function store(array $data): JsonResponse
    {
        $command = $this->createMapper->fromRequest($data);
        $response = $this->createUseCase->execute($command);

        return response()->json($response->toArray(), 201);
    }

    public function show(string $id): JsonResponse
    {
        $response = $this->getByIdUseCase->execute($id);

        return response()->json($response->toArray());
    }

    public function index(): JsonResponse
    {
        $responses = $this->getAllUseCase->execute();

        return response()->json(array_map(
            fn (CustomerResponse $response) => $response->toArray(),
            $responses
        ));
    }

    public function summary(): JsonResponse
    {
        $responses = $this->getAllSummaryUseCase->execute();

        return response()->json(array_map(
            fn (CustomerSummaryResponse $response) => $response->toArray(),
            $responses
        ));
    }

    public function report(): JsonResponse
    {
        $response = $this->reportUseCase->execute();

        return response()->json($response->toArray());
    }

    public function update(array $data, string $id): JsonResponse
    {
        $data['id'] = $id;
        $command = $this->updateMapper->fromRequest($data);
        $response = $this->updateUseCase->execute($command);

        return response()->json($response->toArray());
    }
}
