<?php

namespace App\Providers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //

        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
        
        // Request::macro('routeStarts', function($value) {
        //     return \Str::startsWith(request()->route()->getName(), $value);
        // });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //

        if ($this->app->environment('local')) {
            Mail::alwaysTo('williamconceicaoalmeida@outlook.com');
        }

        Blade::directive('money', function ($amount, $decimals = 2) {
            return "<?php echo number_format($amount, $decimals, ',', '.'); ?>";
        });
    }
}
