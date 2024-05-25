<?php

namespace AzureBlobStorage\Providers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use AzureBlobStorage\Adapters\AzureBlobStorageAdapter;
use AzureBlobStorage\Services\OAuthService;

class AzureBlobStorageServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Register the OAuthService as a singleton
        $this->app->singleton(OAuthService::class, function ($app) {
            return new OAuthService(
                config('azureblobstorage.tenant_id'),
                config('azureblobstorage.client_id'),
                config('azureblobstorage.client_secret')
            );
        });

        // Register the AzureBlobStorageAdapter as a singleton
        $this->app->singleton(AzureBlobStorageAdapter::class, function ($app) {
            return new AzureBlobStorageAdapter(
                config('azureblobstorage.account_name'),
                config('azureblobstorage.container'),
                $app->make(OAuthService::class)
            );
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish the configuration file
        $this->publishes([
            __DIR__ . '/../../config/azureblobstorage.php' => config_path('azureblobstorage.php'),
        ], 'config');

        // Extend the Laravel filesystem to use the custom driver
        Storage::extend('azureblobstorage-driver', function ($app, $config) {
            $adapter = $app->make(AzureBlobStorageAdapter::class);
            return new Filesystem($adapter);
        });
    }
}
