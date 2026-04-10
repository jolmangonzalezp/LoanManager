<?php

declare(strict_types=1);

namespace App\LoanBC\Providers;

use App\LoanBC\Application\UseCases\CreateLoanUseCase;
use App\LoanBC\Application\UseCases\GetAllLoansUseCase;
use App\LoanBC\Application\UseCases\GetLoanByIdUseCase;
use App\LoanBC\Application\UseCases\GetLoanReportUseCase;
use App\LoanBC\Application\UseCases\MakePaymentUseCase;
use App\LoanBC\Domain\Repositories\LoanCreator;
use App\LoanBC\Domain\Repositories\LoanFinderAll;
use App\LoanBC\Domain\Repositories\LoanFinderByCustomerId;
use App\LoanBC\Domain\Repositories\LoanFinderById;
use App\LoanBC\Domain\Repositories\LoanUpdater;
use App\LoanBC\Domain\Services\InterestCalculator;
use App\LoanBC\Infrastructure\Persistence\LoanMapper;
use App\LoanBC\Infrastructure\Repository\EloquentLoanCreator;
use App\LoanBC\Infrastructure\Repository\EloquentLoanFinderAll;
use App\LoanBC\Infrastructure\Repository\EloquentLoanFinderByCustomerId;
use App\LoanBC\Infrastructure\Repository\EloquentLoanFinderById;
use App\LoanBC\Infrastructure\Repository\EloquentLoanUpdater;
use App\LoanBC\Infrastructure\Services\InterestCalculatorService;
use Illuminate\Support\ServiceProvider;

final class LoanServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(LoanMapper::class);
        $this->app->singleton(InterestCalculator::class, InterestCalculatorService::class);

        $this->app->bind(LoanCreator::class, EloquentLoanCreator::class);
        $this->app->bind(LoanFinderById::class, EloquentLoanFinderById::class);
        $this->app->bind(LoanFinderAll::class, EloquentLoanFinderAll::class);
        $this->app->bind(LoanFinderByCustomerId::class, EloquentLoanFinderByCustomerId::class);
        $this->app->bind(LoanUpdater::class, EloquentLoanUpdater::class);

        $this->app->bind(CreateLoanUseCase::class);
        $this->app->bind(GetLoanByIdUseCase::class);
        $this->app->bind(GetAllLoansUseCase::class);
        $this->app->bind(GetLoanReportUseCase::class);
        $this->app->bind(MakePaymentUseCase::class);
    }
}
