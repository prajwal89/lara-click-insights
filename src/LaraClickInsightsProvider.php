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
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');

        $this->publishes([
            __DIR__.'/../config/lara-click-insights.php' => config_path('lara-click-insights.php'),
        ]);

        // publish migrations files to apps /database/migrations
        $this->registerMigrations(__DIR__.'/../database');

        $this->publishes([
            __DIR__.'/../public' => public_path('vendor/prajwal89/lara-click-insights'),
        ], 'public');

        // todo what if user did not publish the assets
        Blade::directive('loadLaraClickInsightsJs', function () {
            return "<?php echo '<script src=\"' . asset('/vendor/prajwal89/lara-click-insights/track-events.js') . '\" defer></script>'; ?>";
        });
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/lara-click-insights.php', 'lara-click-insights');
    }
}
