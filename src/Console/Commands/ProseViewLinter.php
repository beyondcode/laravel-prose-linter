<?php

namespace Beyondcode\LaravelProseLinter\Console\Commands;

use Beyondcode\LaravelProseLinter\Linter\TranslationLinter;
use Beyondcode\LaravelProseLinter\Linter\ViewLinter;
use Illuminate\Console\Command;
use Beyondcode\LaravelProseLinter\LaravelProseLinter;

class ProseViewLinter extends Command
{
    protected $signature = 'lint:blade {viewPath?}';

    protected $description = "Lints all or the selected blade template.";

    public function handle()
    {
        $this->info("🗣  Start linting blade templates");

        $linter = new ViewLinter();

        $linter->lintAll();


        $this->info("🏁 Finished linting.");
    }

}