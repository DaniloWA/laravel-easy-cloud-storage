<?php

namespace Danilowa\LaravelEasyCloudStorage\Providers;

use Illuminate\Support\ServiceProvider;

class EasyCloudStorageServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * This method is used to bind things into the container.
     */
    public function register(): void
    {
        // Merge the package's config file with the application’s config.
        $this->mergeConfigFrom(__DIR__ . '/../Config/easycloudstorage.php', 'easycloudstorage');
    }

    /**
     * Boot the service provider.
     *
     * This method is called after all other service providers have been registered.
     * Here you can perform any additional setup.
     */
    public function boot(): void
    {
        // Check if the application is running in the console
        if ($this->app->runningInConsole()) {
            // Publish the package's configuration file to the application config path
            $this->publishes([
                __DIR__ . '/../Config/easycloudstorage.php' => config_path('easycloudstorage.php'),
            ], 'config');
        }
    }
}
