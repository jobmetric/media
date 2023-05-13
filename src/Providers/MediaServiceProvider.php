<?php

namespace JobMetric\Media\Providers;

use Illuminate\Support\ServiceProvider;
use JobMetric\Metadata\Providers\MetadataServiceProvider;
use JobMetric\Media\MediaService;

class MediaServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('MediaService', function ($app) {
            return new MediaService($app);
        });

        $this->mergeConfigFrom(__DIR__.'/../../config/config.php', 'jmedia');
    }

    /**
     * boot provider
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerMigrations();
        $this->registerPublishables();

        // set translations
        $this->loadTranslationsFrom(realpath(__DIR__.'/../../lang'), 'jmedia');
    }

    /**
     * Register the Passport migration files.
     *
     * @return void
     */
    protected function registerMigrations(): void
    {
        if($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
        }
    }

    /**
     * register publishables
     *
     * @return void
     */
    protected function registerPublishables(): void
    {
        if($this->app->runningInConsole()) {
            // publish config
            $this->publishes([
                realpath(__DIR__.'/../../config/config.php') => config_path('jmedia.php')
            ], 'media-config');

            // publish migration
            $this->publishes([
                realpath(__DIR__.'/../../database/migrations') => database_path('migrations')
            ], 'media-migrations');
        }
    }
}
