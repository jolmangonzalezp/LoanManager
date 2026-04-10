<?php

use App\CustomerBC\Application\Config\CustomerApplicationServiceProvider;
use App\CustomerBC\Infrastructure\Config\CustomerInfrastructureServiceProvider;
use App\LoanBC\Providers\LoanServiceProvider;
use App\PaymentBC\Providers\PaymentServiceProvider;
use App\Providers\AppServiceProvider;
use App\ReportBC\Providers\ReportServiceProvider;
use App\UserBC\Providers\UserServiceProvider;

return [
    AppServiceProvider::class,
    LoanServiceProvider::class,
    PaymentServiceProvider::class,
    ReportServiceProvider::class,
    UserServiceProvider::class,
    CustomerInfrastructureServiceProvider::class,
    CustomerApplicationServiceProvider::class,
];
