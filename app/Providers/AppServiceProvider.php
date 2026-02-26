<?php

namespace App\Providers;

use App\Models\SystemSetting;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Apply dynamic system settings from the database on every request.
        // Wrapped in try/catch so a missing table (fresh install) doesn't crash.
        try {
            $appName  = SystemSetting::get('app_name');
            $timezone = SystemSetting::get('app_timezone');

            if ($appName) {
                config(['app.name' => $appName]);
            }

            if ($timezone) {
                config(['app.timezone' => $timezone]);
                date_default_timezone_set($timezone);
            }
        } catch (\Throwable) {
            // Table not yet migrated — silently skip.
        }
    }
}

