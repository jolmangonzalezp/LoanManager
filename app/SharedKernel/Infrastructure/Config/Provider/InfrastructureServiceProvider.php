<?php

namespace App\SharedKernel\Infrastructure\Config\Provider;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class InfrastructureServiceProvider extends ServiceProvider
{
    public function register() {}

    public function boot()
    {
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}
