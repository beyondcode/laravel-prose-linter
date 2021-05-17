<?php

namespace Beyondcode\LaravelProseLinter\Console\Commands;

use Beyondcode\LaravelProseLinter\Linter\TranslationLinter;
use Illuminate\Console\Command;
use Beyondcode\LaravelProseLinter\LaravelProseLinter;

class ProseViewLinter extends Command
{
    protected $signature = 'lint:blade {viewPath?}';

    protected $description = "Lints all or the selected blade template.";

    public function handle()
    {
        $this->info("🗣  Start linting blade templates");

        $linter = new TranslationLinter();

        $lintingErrors = $linter->all();

        if (count($lintingErrors) > 0) {
            foreach ($lintingErrors as $namespaceKey => $errors) {

                foreach ($errors as $translationKey => $hint) {
                    $this->newLine();
                    $this->warn("{$namespaceKey}.{$translationKey}:");
                    $this->comment($hint);
                }
                $this->newLine(2);

            }
        } else {
            $this->success("✅ No errors, warnings or suggestions found.");
        }

        $this->info("🏁 Finished linting.");
    }

}