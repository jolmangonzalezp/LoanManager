<?php

use App\ReportBC\Presenter\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::middleware('handle.exceptions')->group(function () {
    Route::get('/reports/summary', [ReportController::class, 'summary']);
    Route::get('/reports/portfolio', [ReportController::class, 'portfolio']);
    Route::get('/reports/cash-flow', [ReportController::class, 'cashFlow']);
    Route::get('/reports/profitability', [ReportController::class, 'profitability']);
    Route::get('/reports/delinquency', [ReportController::class, 'delinquency']);
    Route::get('/reports/monthly-collection', [ReportController::class, 'monthlyCollection']);
    Route::get('/reports/kpis', [ReportController::class, 'kpis']);
    Route::get('/reports/audit', [ReportController::class, 'audit']);
    Route::get('/reports/active-loans', [ReportController::class, 'activeLoans']);
    Route::get('/reports/payment-history', [ReportController::class, 'paymentHistory']);
});
