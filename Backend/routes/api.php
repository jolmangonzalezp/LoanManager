<?php

use App\CustomerBC\Presentation\Controllers\CustomerController;
use App\LoanBC\Presentation\Controllers\LoanController;
use App\PaymentBC\Presentation\Controllers\PaymentController;
use App\UserBC\Presentation\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware(['handle.exceptions', 'handle.cors'])->group(function () {
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/auth/me', [AuthController::class, 'me'])->middleware('auth:sanctum');

    Route::get('/customers', [CustomerController::class, 'index']);
    Route::get('/customers/summary', [CustomerController::class, 'summary']);
    Route::get('/customers/report', [CustomerController::class, 'report']);
    Route::get('/customers/{id}', [CustomerController::class, 'show']);
    Route::post('/customers', [CustomerController::class, 'store']);
    Route::put('/customers/{id}', [CustomerController::class, 'update']);

    Route::post('/loans', [LoanController::class, 'store']);
    Route::get('/loans', [LoanController::class, 'index']);
    Route::get('/loans/report', [LoanController::class, 'report']);
    Route::get('/loans/{id}', [LoanController::class, 'show']);

    Route::post('/payments', [PaymentController::class, 'store']);
    Route::get('/payments', [PaymentController::class, 'index']);
});

require __DIR__.'/reports.php';
