<?php

use App\CustomerBC\Presenter\Controllers\CustomerController;
use App\LoanBC\Presenter\Controllers\LoanController;
use App\PaymentBC\Presenter\Controllers\PaymentController;
use App\UserBC\Presentation\Controllers\AuthController;
use App\UserBC\Presentation\Controllers\PermissionController;
use App\UserBC\Presentation\Controllers\RoleController;
use App\UserBC\Presentation\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/ping', fn () => response()->json(['status' => 'ok']));

Route::middleware(['handle.exceptions'])->group(function () {
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);
    Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/auth/me', [AuthController::class, 'me'])->middleware('auth:sanctum');

    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);

    Route::get('/users/{id}/roles', [UserController::class, 'roles']);
    Route::post('/users/{id}/roles', [UserController::class, 'assignRoles']);
    Route::get('/users/{id}/permissions', [UserController::class, 'permissions']);

    Route::get('/roles', [RoleController::class, 'index']);
    Route::get('/roles/{id}', [RoleController::class, 'show']);
    Route::post('/roles', [RoleController::class, 'store']);
    Route::put('/roles/{id}', [RoleController::class, 'update']);
    Route::delete('/roles/{id}', [RoleController::class, 'destroy']);

    Route::get('/permissions', [PermissionController::class, 'index']);
    Route::post('/permissions', [PermissionController::class, 'store']);

    Route::get('/customers', [CustomerController::class, 'index']);
    Route::get('/customers/summary', [CustomerController::class, 'summary']);
    Route::get('/customers/report', [CustomerController::class, 'report']);
    Route::get('/customers/{id}', [CustomerController::class, 'show']);
    Route::get('/customers/{id}/loans', [CustomerController::class, 'loans']);
    Route::post('/customers', [CustomerController::class, 'store']);
    Route::put('/customers/{id}', [CustomerController::class, 'update']);

    Route::post('/loans', [LoanController::class, 'store']);
    Route::get('/loans', [LoanController::class, 'index']);
    Route::get('/loans/report', [LoanController::class, 'report']);
    Route::get('/loans/{id}', [LoanController::class, 'show']);
    Route::put('/loans/{id}', [LoanController::class, 'update']);
    Route::post('/loans/{id}/payment', [LoanController::class, 'makePayment']);

    Route::post('/payments', [PaymentController::class, 'store']);
    Route::get('/payments', [PaymentController::class, 'index']);
    Route::get('/payments/monthly', [PaymentController::class, 'monthly']);
    Route::get('/payments/{id}', [PaymentController::class, 'show']);
    Route::put('/payments/{id}', [PaymentController::class, 'update']);
});

require __DIR__.'/reports.php';


