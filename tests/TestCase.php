<?php

namespace Backendprogramer\RedirectPanel\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as Orchestra;
use Backendprogramer\RedirectPanel\RedirectPanelServiceProvider;
use Livewire\LivewireServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Backendprogramer\\RedirectPanel\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );


    }

    protected function getPackageProviders($app)
    {
        return [
            RedirectPanelServiceProvider::class,
            LivewireServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        Schema::dropAllTables();

        $migration = include __DIR__.'/../database/migrations/create_redirect_panels_table.php.stub';
        $migration->up();
    }
}
