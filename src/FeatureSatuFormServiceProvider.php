<?php

namespace Teguh\FeatureSatuForm;

use Illuminate\Support\ServiceProvider;
use Teguh\FeatureSatuForm\Middleware\AdminAuthenticated;
use Teguh\FeatureSatuForm\Middleware\EnsureSuperAdmin;

class FeatureSatuFormServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     * Tempat untuk binding class ke container atau merge config.
     */
    public function register()
    {
        // Contoh: Menggabungkan config jika ada file config/feature-x.php di submodule
        // $this->mergeConfigFrom(__DIR__.'/../config/feature-x.php', 'feature-x');
    }

    /**
     * Bootstrap services.
     * Tempat untuk loading routes, views, migrations, dll.
     */
    public function boot()
    {
        $router = $this->app['router'];
        $router->aliasMiddleware('admin.auth', AdminAuthenticated::class);
        $router->aliasMiddleware('admin.super', EnsureSuperAdmin::class);

        // 1. Load Routes
        if (file_exists(__DIR__.'/../routes/web.php')) {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        }

        // 2. Load Views
        // Cara panggil di Controller: return view('feature-satu-form::nama-file');
        if (is_dir(__DIR__.'/../resources/views')) {
            $this->loadViewsFrom(__DIR__.'/../resources/views', 'feature-satu-form');
        }

        // 3. Load Migrations (Otomatis jalan saat php artisan migrate)
        if (is_dir(__DIR__.'/../database/migrations')) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }

        // 4. Assets Publishing (Optional)
        // Agar file JS/CSS bisa di-publish ke folder public utama
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'feature-satu-form-migrations');

            $this->publishes([
                __DIR__.'/../database/seeders' => database_path('seeders'),
            ], 'feature-satu-form-seeders');

            $this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/feature-satu-form'),
            ], 'feature-satu-form-assets');
        }
    }
}