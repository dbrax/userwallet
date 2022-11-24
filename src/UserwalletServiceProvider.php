<?php

namespace Epmnzava\Userwallet;

use Illuminate\Support\ServiceProvider;

class UserwalletServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'userwallet');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'userwallet');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('userwallet.php'),
            ], 'config');

            if (!class_exists('CreateWalletLedgerTable') && !class_exists('CreateWalletsTable')) {
                $this->publishes([
                    __DIR__ . '/../database/migrations/create_wallet_ledger_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_wallet_ledger_table.php'),

                    __DIR__ . '/../database/migrations/create_wallets_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_wallets_table.php')



                ], 'migrations');
            }






            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/userwallet'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/userwallet'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/userwallet'),
            ], 'lang');*/

            // Registering package commands.
            // $this->commands([]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'userwallet');

        // Register the main class to use with the facade
        $this->app->singleton('userwallet', function () {
            return new Userwallet;
        });
    }
}
