<?php

declare(strict_types=1);

namespace App\ReportBC\Infrastructure\Config\Provider;

use App\ReportBC\Domain\Service\ReportQueryService;
use App\ReportBC\Presenter\Controllers\ReportController;
use Illuminate\Support\ServiceProvider;

final class ReportServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ReportQueryService::class);
        $this->app->bind(ReportController::class);
    }
}
