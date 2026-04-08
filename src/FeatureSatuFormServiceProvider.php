<?php

namespace Teguh\FeatureSatuForm;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router; // Tambahkan ini

class FeatureSatuFormServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Tempat binding container jika diperlukan
    }

    public function boot(Router $router)
    {
        // Register Middleware
        $router->aliasMiddleware('admin.auth', Middleware\AdminAuthenticated::class);
        $router->aliasMiddleware('admin.super', Middleware\EnsureSuperAdmin::class);

        // 1. Load Routes
        if (file_exists(__DIR__.'/../routes/web.php')) {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        }

        // 2. Load Views
        if (is_dir(__DIR__.'/../resources/views')) {
            $this->loadViewsFrom(__DIR__.'/../resources/views', 'feature-satu-form');
        }

        // 3. Load Migrations
        if (is_dir(__DIR__.'/../database/migrations')) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }

        // 4. Publishing (Hanya jalan di CLI)
        if ($this->app->runningInConsole()) {
            // Publish Migrations
            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'feature-satu-form-migrations');            
            
            // Publish Seeders (User harus menjalankan artisan db:seed manual nanti)
            $this->publishes([
                __DIR__.'/../database/seeders' => database_path('seeders'),
            ], 'feature-satu-form-seeders');

            // Publish Assets
            $this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/feature-satu-form'),
            ], 'feature-satu-form-assets');
        }
    }
}