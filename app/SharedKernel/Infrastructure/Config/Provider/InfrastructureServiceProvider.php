<?php

namespace App\SharedKernel\Infrastructure\Config\Provider;

use Illuminate\Support\ServiceProvider;

class InfrastructureServiceProvider extends ServiceProvider
{
    public function register()
    {

    }

    public function boot()
    {
        // Forzamos HTTPS si el entorno detectado por Laravel es producción
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}
