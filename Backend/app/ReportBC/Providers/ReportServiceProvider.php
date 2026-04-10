<?php

declare(strict_types=1);

namespace App\ReportBC\Providers;

use App\CustomerBC\Domain\Repositories\CustomerFinderById;
use App\LoanBC\Domain\Repositories\LoanFinderAll;
use App\PaymentBC\Domain\Repositories\PaymentFinderByLoanId;
use App\ReportBC\Application\UseCases\GetClientProfitabilityUseCase;
use App\ReportBC\Application\UseCases\GetCollectionAvailabilityUseCase;
use App\ReportBC\Application\UseCases\GetProjectedVsActualUseCase;
use App\ReportBC\Presentation\Controllers\ReportController;
use Illuminate\Support\ServiceProvider;

final class ReportServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(GetProjectedVsActualUseCase::class, function ($app) {
            return new GetProjectedVsActualUseCase(
                $app->make(LoanFinderAll::class),
                $app->make(CustomerFinderById::class),
                $app->make(PaymentFinderByLoanId::class)
            );
        });

        $this->app->bind(GetCollectionAvailabilityUseCase::class, function ($app) {
            return new GetCollectionAvailabilityUseCase(
                $app->make(LoanFinderAll::class),
                $app->make(CustomerFinderById::class),
                $app->make(PaymentFinderByLoanId::class)
            );
        });

        $this->app->bind(GetClientProfitabilityUseCase::class, function ($app) {
            return new GetClientProfitabilityUseCase(
                $app->make(LoanFinderAll::class),
                $app->make(CustomerFinderById::class),
                $app->make(PaymentFinderByLoanId::class)
            );
        });

        $this->app->bind(ReportController::class, function ($app) {
            return new ReportController(
                $app->make(GetProjectedVsActualUseCase::class),
                $app->make(GetCollectionAvailabilityUseCase::class),
                $app->make(GetClientProfitabilityUseCase::class)
            );
        });
    }
}
