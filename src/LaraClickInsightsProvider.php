<?php

namespace Prajwal89\LaraClickInsights;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Prajwal89\LaraClickInsights\Traits\PublishesMigrations;

class LaraClickInsightsProvider extends ServiceProvider
{
    use PublishesMigrations;

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');

        $this->publishes([
            __DIR__ . '/../config/lara-click-insights.php' => config_path('lara-click-insights.php'),
        ], 'lara-click-insights-config');

        // publish migrations files to apps /database/migrations
        $this->registerMigrations(__DIR__ . '/../database');

        $this->publishes([
            __DIR__ . '/../public' => public_path('vendor/prajwal89/lara-click-insights'),
        ], 'lara-click-insights-assets');

        Blade::directive('LaraClickInsightsJs', function () {
            $configs = json_encode([
                'polling_delay_in_sec' => config('lara-click-insights.polling_delay_in_sec'),
                'intersection_threshold' => config('lara-click-insights.intersection_threshold'),
            ]);
            return "<script src=\"{{ asset('/vendor/prajwal89/lara-click-insights/track-events.js') }}\" data-config='" . $configs . ".' defer></script>";
        });
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/lara-click-insights.php', 'lara-click-insights');
    }
}
