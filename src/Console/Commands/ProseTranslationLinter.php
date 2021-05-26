<?php

namespace Beyondcode\LaravelProseLinter\Console\Commands;

use Beyondcode\LaravelProseLinter\Exceptions\LinterException;
use Beyondcode\LaravelProseLinter\Linter\TranslationLinter;
use Illuminate\Console\Command;
use Beyondcode\LaravelProseLinter\LaravelProseLinter;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class ProseTranslationLinter extends Command
{
    protected $signature = 'lint:translation {namespace?* : Translation namespace to lint} {--json : No CLI output. Linting result is stored in storage/}';

    protected $description = "Lints english translations with the Errata AI Vale Linter. Provide either one or several translation namespaces as argument (e.g. 'auth validation') or no argument to lint all translations.";

    public function handle()
    {
        $translationLinter = new TranslationLinter();

        $namespaces = $this->argument("namespace");
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
            } catch (\Exception $exception) {
                $this->warn("({$namespaceToLint}) Unexpected error.");
                if ($verbose) {
                    $this->line($exception->getMessage());
                }
            }

        }

        $tableResults = Arr::flatten($results, 2);

        $totalHints = count($tableResults);
        $this->table(
            ['Namespace', 'Line', 'Position', 'Message', 'Severity', 'Condition'],
            $tableResults
        );
        $this->warn("{$totalHints} linting hints were found.");

        // todo finish output
    }

}