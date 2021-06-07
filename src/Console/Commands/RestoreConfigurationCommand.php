<?php

namespace Beyondcode\LaravelProseLinter\Console\Commands;

use Beyondcode\LaravelProseLinter\Linter\TranslationLinter;
use Beyondcode\LaravelProseLinter\Linter\Vale;
use Beyondcode\LaravelProseLinter\Styles\WriteGood;
use Illuminate\Console\Command;
use Beyondcode\LaravelProseLinter\LaravelProseLinter;

class RestoreConfigurationCommand extends Command
{
    protected $signature = 'lint:restore';

    protected $description = "Restores the linting configuration to the package default.";

    public function handle()
    {
        $v = new Vale();
        $v->restoreIni();

        # TODO reset laravel-prose-linter config!

        $this->info("Configuration restored.");
    }

}