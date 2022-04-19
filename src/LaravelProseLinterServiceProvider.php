<?php

namespace Beyondcode\LaravelProseLinter;

use Beyondcode\LaravelProseLinter\Console\Commands\LintTranslationCommand;
use Beyondcode\LaravelProseLinter\Console\Commands\LintViewCommand;
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
                __DIR__.'/../config/config.php' => config_path('linter.php'),
            ], 'linting-config');

            // Publish the style files
            $this->publishes([
                __DIR__.'/../resources/styles' => resource_path('laravel-prose-linter'),
            ], 'linting-styles');

            // Register package commands
            $this->commands([
                LintTranslationCommand::class,
                LintViewCommand::class,
            ]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'linter');
    }
}
