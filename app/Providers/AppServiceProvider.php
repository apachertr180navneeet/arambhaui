<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use App\Models\CompanySetting;

if (!function_exists('format_quantity')) {
    function format_quantity($val, $maxDecimals = 2) {
        if ($val === null || $val === '') return '0';
        $floatVal = (float)$val;
        if ($floatVal == (int)$floatVal) {
            return (string)(int)$floatVal;
        }
        $formatted = number_format($floatVal, $maxDecimals, '.', '');
        return rtrim(rtrim($formatted, '0'), '.');
    }
}

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        require_once app_path('helpers.php');
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

        $companyName = !empty($companySettings['company_name']) ? $companySettings['company_name'] : config('app.name', 'Nathmal Amarchand');

        View::share('companySettings', $companySettings);
        View::share('companyName', $companyName);

        Blade::directive('formatQty', function ($expression) {
            return "<?php echo format_quantity($expression); ?>";
        });
    }
}
