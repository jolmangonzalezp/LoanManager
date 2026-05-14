<?php

declare(strict_types=1);

namespace App\RouteBC\Infrastructure\Config\Provider;

use App\RouteBC\Application\UseCase\AssignUsersToRouteUseCase;
use App\RouteBC\Application\UseCase\CreateRouteUseCase;
use App\RouteBC\Application\UseCase\CreateZoneUseCase;
use App\RouteBC\Application\UseCase\DeleteZoneUseCase;
use App\RouteBC\Application\UseCase\GetMapDataUseCase;
use App\RouteBC\Application\UseCase\UpdateZoneUseCase;
use App\RouteBC\Domain\Repository\RouteRepositoryInterface;
use App\RouteBC\Domain\Repository\ZoneRepositoryInterface;
use App\RouteBC\Domain\Service\GeoLocationService;
use App\RouteBC\Infrastructure\Mapper\RouteMapper;
use App\RouteBC\Infrastructure\Mapper\ZoneMapper;
use App\RouteBC\Infrastructure\Persistence\Repository\EloquentRouteRepository;
use App\RouteBC\Infrastructure\Persistence\Repository\EloquentZoneRepository;
use App\RouteBC\Presenter\Controllers\RouteController;
use App\RouteBC\Presenter\Controllers\ZoneController;
use Illuminate\Support\ServiceProvider;

final class RouteServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ZoneMapper::class);
        $this->app->singleton(RouteMapper::class);

        $this->app->bind(ZoneRepositoryInterface::class, EloquentZoneRepository::class);
        $this->app->bind(RouteRepositoryInterface::class, EloquentRouteRepository::class);

        $this->app->bind(GeoLocationService::class);

        $this->app->bind(CreateZoneUseCase::class);
        $this->app->bind(UpdateZoneUseCase::class);
        $this->app->bind(DeleteZoneUseCase::class);
        $this->app->bind(CreateRouteUseCase::class);
        $this->app->bind(AssignUsersToRouteUseCase::class);
        $this->app->bind(GetMapDataUseCase::class);

        $this->app->singleton(ZoneController::class);
        $this->app->singleton(RouteController::class);
    }
}
