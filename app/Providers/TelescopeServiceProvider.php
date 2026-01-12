<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeApplicationServiceProvider;

class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
    protected function gate(): void
    {
        Gate::define('viewTelescope', function ($user) {
            return $user && $user->role === 'admin';
        });
    }
}
