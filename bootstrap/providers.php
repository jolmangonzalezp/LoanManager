<?php

use App\CustomerBC\Infrastructure\Config\Provider\CustomerServiceProvider;
use App\LoanBC\Infrastructure\Config\Provider\LoanServiceProvider;
use App\PaymentBC\Infrastructure\Config\Provider\PaymentServiceProvider;
use App\ReportBC\Infrastructure\Config\Provider\ReportServiceProvider;
use App\SharedKernel\Infrastructure\Config\Provider\InfrastructureServiceProvider;
use App\RouteBC\Infrastructure\Config\Provider\RouteServiceProvider;
use App\UserBC\Infrastructure\Config\Provider\UserServiceProvider;

return [
    CustomerServiceProvider::class,
    LoanServiceProvider::class,
    PaymentServiceProvider::class,
    ReportServiceProvider::class,
    UserServiceProvider::class,
    RouteServiceProvider::class,
    InfrastructureServiceProvider::class,
];
