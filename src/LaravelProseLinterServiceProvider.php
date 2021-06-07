<?php

namespace Beyondcode\LaravelProseLinter;

use Beyondcode\LaravelProseLinter\Console\Commands\LintTranslationCommand;
use Beyondcode\LaravelProseLinter\Console\Commands\LintViewCommand;
use Beyondcode\LaravelProseLinter\Console\Commands\RestoreConfigurationCommand;
use Illuminate\Support\ServiceProvider;

class LaravelProseLinterServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('linter.php'),
            ], 'config');

            // Publish the style files
            $this->publishes([
                __DIR__ . '/../resources/styles' => resource_path('lang/vendor/laravel-prose-linter'),
            ], 'linting-styles');

            // Register package commands
            $this->commands([
                LintTranslationCommand::class,
                LintViewCommand::class,
                RestoreConfigurationCommand::class
            ]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'laravel-prose-linter');
    }
}
