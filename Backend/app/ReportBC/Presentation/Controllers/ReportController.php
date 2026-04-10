<?php

declare(strict_types=1);

namespace App\ReportBC\Presentation\Controllers;

use App\ReportBC\Application\UseCases\GetClientProfitabilityUseCase;
use App\ReportBC\Application\UseCases\GetCollectionAvailabilityUseCase;
use App\ReportBC\Application\UseCases\GetProjectedVsActualUseCase;
use Illuminate\Http\JsonResponse;

final class ReportController
{
    public function __construct(
        private readonly GetProjectedVsActualUseCase $projectedVsActual,
        private readonly GetCollectionAvailabilityUseCase $collectionAvailability,
        private readonly GetClientProfitabilityUseCase $clientProfitability
    ) {}

    public function projectedVsActual(): JsonResponse
    {
        $responses = $this->projectedVsActual->execute();

        return response()->json(array_map(
            fn ($r) => $r->toArray(),
            $responses
        ));
    }

    public function collectionAvailability(): JsonResponse
    {
        $responses = $this->collectionAvailability->execute();

        return response()->json(array_map(
            fn ($r) => $r->toArray(),
            $responses
        ));
    }

    public function clientProfitability(): JsonResponse
    {
        $responses = $this->clientProfitability->execute();

        return response()->json(array_map(
            fn ($r) => $r->toArray(),
            $responses
        ));
    }
}
