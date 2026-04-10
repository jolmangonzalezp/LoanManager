<?php

namespace App\Providers;

use App\LoanBC\Domain\Ports\CustomerLoanStatistics;
use App\LoanBC\Infrastructure\Persistence\StubCustomerLoanStatistics;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(
            CustomerLoanStatistics::class,
            StubCustomerLoanStatistics::class
        );
    }

    public function boot(): void
    {
        //
    }
}
