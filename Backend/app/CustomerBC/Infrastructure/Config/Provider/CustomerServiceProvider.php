<?php

declare(strict_types=1);

namespace App\CustomerBC\Infrastructure\Config\Provider;

use App\CustomerBC\Application\UseCase\CreateCustomerUseCase;
use App\CustomerBC\Application\UseCase\GetAllCustomersSummaryUseCase;
use App\CustomerBC\Application\UseCase\GetAllCustomersUseCase;
use App\CustomerBC\Application\UseCase\GetCustomerByIdUseCase;
use App\CustomerBC\Application\UseCase\GetCustomerNamesUseCase;
use App\CustomerBC\Application\UseCase\UpdateCustomerUseCase;
use App\CustomerBC\Domain\Repository\CustomerCreator;
use App\CustomerBC\Domain\Repository\CustomerDniFinder;
use App\CustomerBC\Domain\Repository\CustomerFinderAll;
use App\CustomerBC\Domain\Repository\CustomerFinderById;
use App\CustomerBC\Domain\Repository\CustomerUpdater;
use App\CustomerBC\Domain\Repository\FindActiveCustomers;
use App\CustomerBC\Domain\Repository\FindActiveCustomersByIds;
use App\CustomerBC\Infrastructure\Mapper\CustomerMapper;
use App\CustomerBC\Infrastructure\Persistence\Repository\EloquentCustomerCreator;
use App\CustomerBC\Infrastructure\Persistence\Repository\EloquentCustomerDniFinder;
use App\CustomerBC\Infrastructure\Persistence\Repository\EloquentCustomerFinderAll;
use App\CustomerBC\Infrastructure\Persistence\Repository\EloquentCustomerFinderById;
use App\CustomerBC\Infrastructure\Persistence\Repository\EloquentCustomerRepository;
use App\CustomerBC\Infrastructure\Persistence\Repository\EloquentCustomerUpdater;
use App\CustomerBC\Presenter\Controllers\CustomerController;
use App\SharedKernel\Application\Services\AuditLogger;
use App\SharedKernel\Domain\Ports\EncryptionService;
use App\SharedKernel\Domain\Ports\MaskingService;
use App\SharedKernel\Infrastructure\Services\LaravelEncryptionService;
use App\SharedKernel\Infrastructure\Services\LaravelMaskingService;
use Illuminate\Support\ServiceProvider;

final class CustomerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Shared Kernel Bindings
        $this->app->singleton(AuditLogger::class);

        // Infrastructure Bindings
        $this->app->singleton(EncryptionService::class, LaravelEncryptionService::class);
        $this->app->singleton(MaskingService::class, LaravelMaskingService::class);
        $this->app->singleton(CustomerMapper::class);

        $this->app->bind(CustomerCreator::class, EloquentCustomerCreator::class);
        $this->app->bind(CustomerFinderById::class, EloquentCustomerFinderById::class);
        $this->app->bind(CustomerFinderAll::class, EloquentCustomerFinderAll::class);
        $this->app->bind(CustomerUpdater::class, EloquentCustomerUpdater::class);
        $this->app->bind(CustomerDniFinder::class, EloquentCustomerDniFinder::class);

        // New Repositories for Segregation
        $this->app->bind(FindActiveCustomers::class, EloquentCustomerRepository::class);
        $this->app->bind(FindActiveCustomersByIds::class, EloquentCustomerRepository::class);

        // Application Bindings
        $this->app->singleton(CreateCustomerUseCase::class);
        $this->app->singleton(GetCustomerByIdUseCase::class);
        $this->app->singleton(GetAllCustomersUseCase::class);
        $this->app->singleton(GetAllCustomersSummaryUseCase::class);
        $this->app->singleton(UpdateCustomerUseCase::class);
        $this->app->singleton(GetCustomerNamesUseCase::class);

        // Presentation Bindings
        $this->app->singleton(CustomerController::class);
    }
}
