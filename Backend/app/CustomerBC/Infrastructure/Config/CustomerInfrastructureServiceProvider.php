<?php

namespace App\CustomerBC\Infrastructure\Config;

use App\CustomerBC\Domain\Repositories\CustomerCreator;
use App\CustomerBC\Domain\Repositories\CustomerDniFinder;
use App\CustomerBC\Domain\Repositories\CustomerFinderAll;
use App\CustomerBC\Domain\Repositories\CustomerFinderById;
use App\CustomerBC\Domain\Repositories\CustomerUpdater;
use App\CustomerBC\Infrastructure\Persistence\CustomerMapper;
use App\CustomerBC\Infrastructure\Repository\EloquentCustomerCreator;
use App\CustomerBC\Infrastructure\Repository\EloquentCustomerDniFinder;
use App\CustomerBC\Infrastructure\Repository\EloquentCustomerFinderAll;
use App\CustomerBC\Infrastructure\Repository\EloquentCustomerFinderById;
use App\CustomerBC\Infrastructure\Repository\EloquentCustomerUpdater;
use App\SharedKernel\Domain\Ports\EncryptionService;
use App\SharedKernel\Domain\Ports\MaskingService;
use App\SharedKernel\Infrastructure\Services\LaravelEncryptionService;
use App\SharedKernel\Infrastructure\Services\LaravelMaskingService;
use Illuminate\Support\ServiceProvider;

class CustomerInfrastructureServiceProvider extends ServiceProvider
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
    }
}
