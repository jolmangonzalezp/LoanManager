<?php

declare(strict_types=1);

namespace App\LoanBC\Infrastructure\Config\Provider;

use App\LoanBC\Application\UseCase\CreateLoanUseCase;
use App\LoanBC\Application\UseCase\GetAllLoansUseCase;
use App\LoanBC\Application\UseCase\GetLoansByCustomerUseCase;
use App\LoanBC\Application\UseCase\GetLoanByIdUseCase;
use App\LoanBC\Application\UseCase\GetLoanReportUseCase;
use App\LoanBC\Application\UseCase\MakePaymentUseCase;
use App\LoanBC\Application\UseCase\UpdateLoanUseCase;
use App\LoanBC\Domain\Repository\CustomerNameProvider;
use App\LoanBC\Domain\Repository\LoanCreator;
use App\LoanBC\Domain\Repository\LoanFinderAll;
use App\LoanBC\Domain\Repository\LoanFinderByCustomerId;
use App\LoanBC\Domain\Repository\LoanFinderById;
use App\LoanBC\Domain\Repository\LoanUpdater;
use App\LoanBC\Domain\Services\InterestCalculator;
use App\LoanBC\Domain\Services\LoanNumberGenerator;
use App\LoanBC\Infrastructure\Mapper\LoanMapper;
use App\LoanBC\Infrastructure\Persistence\Repository\CustomerNameAdapter;
use App\LoanBC\Infrastructure\Persistence\Repository\EloquentLoanCreator;
use App\LoanBC\Infrastructure\Persistence\Repository\EloquentLoanFinderAll;
use App\LoanBC\Infrastructure\Persistence\Repository\EloquentLoanFinderByCustomerId;
use App\LoanBC\Infrastructure\Persistence\Repository\EloquentLoanFinderById;
use App\LoanBC\Infrastructure\Persistence\Repository\EloquentLoanUpdater;
use App\LoanBC\Infrastructure\Services\InterestCalculatorService;
use App\LoanBC\Presenter\Controllers\LoanController;
use App\SharedKernel\Application\Services\AuditLogger;
use Illuminate\Support\ServiceProvider;

final class LoanServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Shared Kernel Bindings
        $this->app->singleton(AuditLogger::class);

        $this->app->bind(CustomerNameProvider::class, CustomerNameAdapter::class);
        $this->app->singleton(LoanMapper::class);
        $this->app->singleton(InterestCalculator::class, InterestCalculatorService::class);
        $this->app->singleton(LoanNumberGenerator::class);

        $this->app->bind(LoanCreator::class, EloquentLoanCreator::class);
        $this->app->bind(LoanFinderById::class, EloquentLoanFinderById::class);
        $this->app->bind(LoanFinderAll::class, EloquentLoanFinderAll::class);
        $this->app->bind(LoanFinderByCustomerId::class, EloquentLoanFinderByCustomerId::class);
        $this->app->bind(LoanUpdater::class, EloquentLoanUpdater::class);

        $this->app->bind(CreateLoanUseCase::class);
        $this->app->bind(GetLoanByIdUseCase::class);
        $this->app->bind(GetAllLoansUseCase::class);
        $this->app->bind(GetLoansByCustomerUseCase::class);
        $this->app->bind(GetLoanReportUseCase::class);
        $this->app->bind(MakePaymentUseCase::class);
        $this->app->bind(UpdateLoanUseCase::class);

        $this->app->singleton(LoanController::class);
    }
}
