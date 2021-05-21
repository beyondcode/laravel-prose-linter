<?php

namespace Beyondcode\LaravelProseLinter\Console\Commands;

use Beyondcode\LaravelProseLinter\Linter\TranslationLinter;
use Illuminate\Console\Command;
use Beyondcode\LaravelProseLinter\LaravelProseLinter;

class ProseTranslationLinter extends Command
{
    protected $signature = 'lint:translation {namespace?}';

    protected $description = "Lints all translations ";

    public function handle()
    {
        // toDo: namespace
        $this->info("🗣  Start linting translations");

        $linter = new TranslationLinter();

        $linter->lintAll();

        if ($linter->hasErrors()) {
            // go through namespaces
            foreach ($linter->getResults() as $namespaceKey => $results) {

                // go through translations in namespace
                foreach ($results as $lintingResult) {

                    $this->newLine();
                    $this->warn("{$namespaceKey}.{$lintingResult->getTextIdentifier()}:");

                    // go through hints in translation linting result
                    foreach($lintingResult->getHints() as $hint) {
                        $this->line($hint->toCliOutput());
                    }

                }

                $this->newLine(2);

            }
        } else {
            $this->info("✅ No errors, warnings or suggestions found.");
        }

        $this->info("🏁 Finished linting.");
    }

}