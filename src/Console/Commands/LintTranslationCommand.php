<?php

namespace Beyondcode\LaravelProseLinter\Console\Commands;

use Exception;
use Illuminate\Support\Arr;
use Beyondcode\LaravelProseLinter\Linter\TranslationLinter;

class LintTranslationCommand extends LinterCommand
{
    protected $signature = 'lint:translation {namespace?* : Translation namespace to lint} {--json : No CLI output. Linting result is stored in storage/}';

    protected $description = "Lints english translations with the Errata AI Vale Linter. Provide either one or several translation namespaces as argument (e.g. 'auth validation') or no argument to lint all translations.";

    public function handle()
    {
        $translationLinter = new TranslationLinter();

        // collect command input
        $namespaces = $this->argument("namespace");
        $outputAsJson = $this->option("json") ? true : false;
        $verbose = $this->option("verbose");

        $namespacesToLint = empty($namespaces) ? $translationLinter->getTranslationFiles() : $namespaces;
        $totalNamespacesToLint = count($namespacesToLint);

        $this->info("🗣  Start linting ...");
        $startTime = microtime(true);

        // create progress bar
        $progressBar = $this->output->createProgressBar($totalNamespacesToLint);

        $results = [];
        foreach ($namespacesToLint as $namespaceToLint) {

            try {
                $results[] = $translationLinter->lintNamespace($namespaceToLint);
                $progressBar->advance();
            } catch (Exception $exception) {
                $this->warn("({$namespaceToLint}) Unexpected error.");
                if ($verbose) {
                    $this->line($exception->getMessage());
                }
            }

        }

        $tableResults = Arr::flatten($results, 2);

        $lintingDuration = round(microtime(true) - $startTime, 2);
        $progressBar->finish();

        $this->finishLintingOutput($tableResults, $outputAsJson, $lintingDuration);
    }

}