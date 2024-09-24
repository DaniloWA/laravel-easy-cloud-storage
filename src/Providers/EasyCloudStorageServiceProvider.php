<?php

namespace Danilowa\LaravelEasyCloudStorage\Providers;

use Illuminate\Support\ServiceProvider;
use Danilowa\LaravelEasyCloudStorage\EasyStorage;

class EasyCloudStorageServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * This method is used to bind things into the container.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../Config/easycloudstorage.php', 'easycloudstorage');
        $this->app->singleton('easy-storage', function ($app) {
            return new EasyStorage();
        });
    }

    /**
     * Boot the service provider.
     *
     * This method is called after all other service providers have been registered.
     * Here you can perform any additional setup.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../Config/easycloudstorage.php' => config_path('easycloudstorage.php'),
            ], 'config');
        }
    }
}
