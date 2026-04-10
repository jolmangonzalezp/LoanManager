<?php

namespace App\CustomerBC\Providers;

use App\CustomerBC\Application\UseCases\CreateCustomerUseCase;
use App\CustomerBC\Application\UseCases\GetAllCustomersSummaryUseCase;
use App\CustomerBC\Application\UseCases\GetAllCustomersUseCase;
use App\CustomerBC\Application\UseCases\GetCustomerByIdUseCase;
use App\CustomerBC\Application\UseCases\GetCustomerReportUseCase;
use App\CustomerBC\Application\UseCases\UpdateCustomerUseCase;
use App\CustomerBC\Domain\Repositories\CustomerCreator;
use App\CustomerBC\Domain\Repositories\CustomerDniFinder;
use App\CustomerBC\Domain\Repositories\CustomerFinderAll;
use App\CustomerBC\Domain\Repositories\CustomerFinderById;
use App\CustomerBC\Domain\Repositories\CustomerUpdater;
use App\CustomerBC\Infrastructure\Persistence\CustomerMapper;
use App\CustomerBC\Infrastructure\Persistence\EloquentCustomerCreator;
use App\CustomerBC\Infrastructure\Persistence\EloquentCustomerDniFinder;
use App\CustomerBC\Infrastructure\Persistence\EloquentCustomerFinderAll;
use App\CustomerBC\Infrastructure\Persistence\EloquentCustomerFinderById;
use App\CustomerBC\Infrastructure\Persistence\EloquentCustomerUpdater;
use App\CustomerBC\Presentation\Controllers\CustomerController;
use App\SharedKernel\Domain\Ports\EncryptionService;
use App\SharedKernel\Domain\Ports\MaskingService;
use App\SharedKernel\Infrastructure\Services\LaravelEncryptionService;
use App\SharedKernel\Infrastructure\Services\LaravelMaskingService;
use Illuminate\Support\ServiceProvider;

final class CustomerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(EncryptionService::class, LaravelEncryptionService::class);
        $this->app->singleton(MaskingService::class, LaravelMaskingService::class);

        $this->app->singleton(CustomerMapper::class);

        $this->app->bind(CustomerCreator::class, EloquentCustomerCreator::class);
        $this->app->bind(CustomerFinderById::class, EloquentCustomerFinderById::class);
        $this->app->bind(CustomerFinderAll::class, EloquentCustomerFinderAll::class);
        $this->app->bind(CustomerUpdater::class, EloquentCustomerUpdater::class);
        $this->app->bind(CustomerDniFinder::class, EloquentCustomerDniFinder::class);

        $this->app->singleton(CreateCustomerUseCase::class);
        $this->app->singleton(GetCustomerByIdUseCase::class);
        $this->app->singleton(GetAllCustomersUseCase::class);
        $this->app->singleton(GetAllCustomersSummaryUseCase::class);
        $this->app->singleton(UpdateCustomerUseCase::class);
        $this->app->singleton(GetCustomerReportUseCase::class);

        $this->app->singleton(CustomerController::class);
    }
}
