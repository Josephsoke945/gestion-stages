<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Spatie\Permission\PermissionServiceProvider as SpatiePermissionServiceProvider;

class PermissionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->register(SpatiePermissionServiceProvider::class);
    }

    public function boot(): void
    {
        //
    }
} 