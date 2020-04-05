<?php

namespace Nksoft\Articles;

use Illuminate\Support\ServiceProvider;

class NkSoftArticlesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/routes/api.php');
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        $this->loadViewsFrom(__DIR__ . '/views', 'articles');
        $this->loadTranslationsFrom(__DIR__ . '/language', 'nksoft');
        $this->publishes([
            __DIR__ . '/language' => resource_path('lang/vendor/nksoft'),
        ], 'nksoft');
        $this->mergeConfigFrom(__DIR__ . '/config/nksoft.php', 'nksoft');
    }
}
