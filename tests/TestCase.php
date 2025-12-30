<?php

namespace Epmnzava\Userwallet\Tests;


use Orchestra\Testbench\TestCase as Orchestra;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        // Create tables for in-memory sqlite tests.
        // If your package already ships migrations, replace this with:
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->setUpDatabaseTables();
    }

    protected function getPackageProviders($app)
    {
        // If you have a ServiceProvider, register it here.
        // return [\Epmnzava\Userwallet\UserwalletServiceProvider::class];

        return [];
    }

    protected function defineEnvironment($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    private function setUpDatabaseTables(): void
    {
        Schema::dropAllTables();

        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('userid')->index();
            $table->float('balance',8,2)->nullable();// stored as string/decimal-like
            $table->string('walletID')->nullable();
            $table->string('source')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('wallet_ledgers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('userid')->index();
            $table->string('amount');
            $table->string('type')->nullable(); // deposit/withdraw
            $table->string('source')->nullable();
            $table->string('destination')->nullable();
            $table->string('receipt')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }
}
