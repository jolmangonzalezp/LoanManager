<?php

use App\ReportBC\Application\UseCases\GetClientProfitabilityUseCase;
use App\ReportBC\Application\UseCases\GetCollectionAvailabilityUseCase;
use App\ReportBC\Application\UseCases\GetProjectedVsActualUseCase;
use Illuminate\Support\Facades\Route;

Route::middleware('handle.exceptions')->group(function () {
    Route::get('/reports/projected-vs-actual', function () {
        $useCase = app(GetProjectedVsActualUseCase::class);
        $responses = $useCase->execute();

        return response()->json(array_map(fn ($r) => $r->toArray(), $responses));
    });

    Route::get('/reports/collection-availability', function () {
        $useCase = app(GetCollectionAvailabilityUseCase::class);
        $responses = $useCase->execute();

        return response()->json(array_map(fn ($r) => $r->toArray(), $responses));
    });

    Route::get('/reports/client-profitability', function () {
        $useCase = app(GetClientProfitabilityUseCase::class);
        $responses = $useCase->execute();

        return response()->json(array_map(fn ($r) => $r->toArray(), $responses));
    });
});
