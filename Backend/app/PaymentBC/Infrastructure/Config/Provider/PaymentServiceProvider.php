<?php

declare(strict_types=1);

namespace App\PaymentBC\Infrastructure\Config\Provider;

use App\PaymentBC\Application\UseCase\GetAllPaymentsUseCase;
use App\PaymentBC\Application\UseCase\GetMonthlyReportUseCase;
use App\PaymentBC\Application\UseCase\ProcessPaymentUseCase;
use App\PaymentBC\Domain\Repository\PaymentCreator;
use App\PaymentBC\Domain\Repository\PaymentFinderAll;
use App\PaymentBC\Domain\Repository\PaymentFinderByLoanId;
use App\PaymentBC\Infrastructure\Mapper\PaymentMapper;
use App\PaymentBC\Infrastructure\Persistence\Repository\EloquentPaymentCreator;
use App\PaymentBC\Infrastructure\Persistence\Repository\EloquentPaymentFinderAll;
use App\PaymentBC\Infrastructure\Persistence\Repository\EloquentPaymentFinderByLoanId;
use Illuminate\Support\ServiceProvider;

final class PaymentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PaymentMapper::class);
        $this->app->bind(PaymentCreator::class, EloquentPaymentCreator::class);
        $this->app->bind(PaymentFinderAll::class, EloquentPaymentFinderAll::class);
        $this->app->bind(PaymentFinderByLoanId::class, EloquentPaymentFinderByLoanId::class);

        $this->app->bind(ProcessPaymentUseCase::class);
        $this->app->bind(GetAllPaymentsUseCase::class);
        $this->app->bind(GetMonthlyReportUseCase::class);
        $this->app->bind(GetLoanBalanceReportUseCase::class);
    }
}
