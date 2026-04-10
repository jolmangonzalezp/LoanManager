<?php

namespace App\CustomerBC\Application\Config;

use App\CustomerBC\Application\UseCases\CreateCustomerUseCase;
use App\CustomerBC\Application\UseCases\GetAllCustomersSummaryUseCase;
use App\CustomerBC\Application\UseCases\GetAllCustomersUseCase;
use App\CustomerBC\Application\UseCases\GetCustomerByIdUseCase;
use App\CustomerBC\Application\UseCases\GetCustomerReportUseCase;
use App\CustomerBC\Application\UseCases\UpdateCustomerUseCase;
use App\CustomerBC\Presentation\Controllers\CustomerController;
use App\CustomerBC\Presentation\Mappers\CreateCustomerRequestMapper;
use App\CustomerBC\Presentation\Mappers\UpdateCustomerRequestMapper;
use Illuminate\Support\ServiceProvider;

class CustomerApplicationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CreateCustomerUseCase::class);
        $this->app->singleton(GetCustomerByIdUseCase::class);
        $this->app->singleton(GetAllCustomersUseCase::class);
        $this->app->singleton(GetAllCustomersSummaryUseCase::class);
        $this->app->singleton(UpdateCustomerUseCase::class);
        $this->app->singleton(GetCustomerReportUseCase::class);

        $this->app->singleton(CreateCustomerRequestMapper::class);
        $this->app->singleton(UpdateCustomerRequestMapper::class);

        $this->app->singleton(CustomerController::class);
    }
}
