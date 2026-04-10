<?php

declare(strict_types=1);

namespace App\PaymentBC\Providers;

use App\LoanBC\Domain\Repositories\LoanFinderById;
use App\LoanBC\Domain\Repositories\LoanUpdater;
use App\PaymentBC\Application\UseCases\ProcessPaymentUseCase;
use App\PaymentBC\Domain\Repositories\PaymentCreator;
use App\PaymentBC\Domain\Repositories\PaymentFinderByLoanId;
use App\PaymentBC\Infrastructure\Repositories\EloquentPaymentCreator;
use App\PaymentBC\Infrastructure\Repositories\EloquentPaymentFinderByLoanId;
use App\PaymentBC\Infrastructure\Repositories\PaymentMapper;
use Illuminate\Support\ServiceProvider;

final class PaymentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PaymentMapper::class);
        $this->app->bind(PaymentCreator::class, EloquentPaymentCreator::class);
        $this->app->bind(PaymentFinderByLoanId::class, EloquentPaymentFinderByLoanId::class);

        $this->app->bind(ProcessPaymentUseCase::class, function ($app) {
            return new ProcessPaymentUseCase(
                $app->make(PaymentCreator::class),
                $app->make(LoanFinderById::class),
                $app->make(LoanUpdater::class)
            );
        });
    }
}
