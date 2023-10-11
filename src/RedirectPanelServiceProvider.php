<?php

namespace Backendprogramer\RedirectPanel;

use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Backendprogramer\RedirectPanel\Commands\RedirectPanelCommand;
use Backendprogramer\RedirectPanel\Livewire\RedirectPanelManagement;

class RedirectPanelServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-redirect-panel')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_redirect_panels_table')
            ->hasCommand(RedirectPanelCommand::class)
            ->hasTranslations();
    }

    public function packageRegistered()
    {
        Route::macro('redirectPanel', function (string $baseUrl) {
            Route::prefix($baseUrl)->group(function () {
                Route::get('/', RedirectPanelManagement::class);
            });
        });
    }
    public function boot()
    {
        parent::boot();

        Livewire::component('redirect-panel-management', RedirectPanelManagement::class);

        app()->setLocale(config('redirect-panel.locale', 'auto'));
    }
}
