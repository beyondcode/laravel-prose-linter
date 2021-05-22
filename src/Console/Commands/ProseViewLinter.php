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

        if ($linter->hasErrors()) {
            // go through namespaces
            foreach ($linter->getResults() as $namespaceKey => $lintingResult) {

                $this->newLine();
                $this->warn("{$lintingResult->getTextIdentifier()}.blade.php:");

                // go through hints in translation linting result
                foreach ($lintingResult->getHints() as $hint) {
                    $this->line($hint->toCliOutput());
                }

            }
        } else {
            $this->info("✅ No errors, warnings or suggestions found.");
        }

        $this->info("🏁 Finished linting.");
    }

}