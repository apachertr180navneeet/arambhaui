<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\Models\CompanySetting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (!$this->app->environment('testing') && (str_starts_with((string) config('app.url'), 'https://') || request()->isSecure() || request()->header('x-forwarded-proto') === 'https')) {
            URL::forceScheme('https');
        }

        try {
            if (Schema::hasTable('company_settings')) {
                $companySettings = CompanySetting::all()->pluck('value', 'key')->toArray();
            } else {
                $companySettings = [];
            }
        } catch (\Throwable $e) {
            $companySettings = [];
        }

        $companyName = !empty($companySettings['company_name']) ? $companySettings['company_name'] : config('app.name', 'GarmentERP');

        View::share('companySettings', $companySettings);
        View::share('companyName', $companyName);
    }
}
