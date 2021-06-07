<?php

namespace Beyondcode\LaravelProseLinter\Console\Commands;

use Beyondcode\LaravelProseLinter\Linter\ViewLinter;
use Beyondcode\LaravelProseLinter\Exceptions\LinterException;

class View extends Linter
{
    protected $signature = 'lint:blade {bladeTemplate? : Template key for a single blade template} {--exclude= : directories to exclude in format dir1,dir2,dir3 } {--json : No CLI output. Linting result is stored in storage/ }';

    protected $description = "Lints blade templates with the Errata AI Vale Linter. Provide either a single blade template to lint or directories to exclude - or no arguments at all to lint all blade templates.";

    public function handle()
    {
        $viewLinter = new ViewLinter();

        // analyze argument and options input and ask for confirmation if necessary / abort if invalid
        $singleBladeTemplate = $this->argument("bladeTemplate");
        $directoriesToExclude = $this->option("exclude") !== null ? explode(",", $this->option("exclude")) : [];

        $outputAsJson = $this->option("json") ? true : false;
        $verbose = $this->option("verbose") ? true : false;

        if ($singleBladeTemplate === null && empty($directoriesToExclude)) {
            if (!$this->confirm("Are you sure you want to lint all blade files in your application?")) {
                $this->line("❌ Linting aborted.");
                return;
            }
        } elseif ($singleBladeTemplate !== null && !empty($directoriesToExclude)) {
            $this->error("Invalid parameters. Please provide either a single template key to lint or directories to exclude or no further options to lint all blade templates.");
            return;
        }

        // collect blade files to lint
        $templatesToLint = [];
        if ($singleBladeTemplate !== null) {
            $this->line("Linting single blade template with key '{$singleBladeTemplate}'.");
            $templatesToLint[] = $singleBladeTemplate;
            $totalFilesToLint = count($templatesToLint);
        } else {
            $templatesToLint = $viewLinter->readBladeKeys($directoriesToExclude);
            $totalFilesToLint = count($templatesToLint);

            $message = "Linting all blade templates";

            if (!empty($directoriesToExclude)) {
                $message .= ", excluding: " . implode(", ", $directoriesToExclude);
            }
            $this->line($message);

            $this->line("Found {$totalFilesToLint} blade files.");
        }

        $this->info("🗣  Start linting ...");
        $startTime = microtime(true);

        // create progress bar
        $progressBar = $this->output->createProgressBar($totalFilesToLint);

        $results = [];
        foreach ($templatesToLint as $templateToLint) {

            try {
                $progressBar->advance();

                $filePath = $viewLinter->createLintableCopy($templateToLint);
                $viewLinter->lintFile($filePath, $templateToLint);
            } catch (LinterException $linterException) {
                $results = array_merge($results, $linterException->getResult()->toArray());
            } catch (\Exception $exception) {
                $this->warn("({$templateToLint}) Unexpected error.");
                if ($verbose) {
                    $this->line($exception->getMessage());
                }
            } finally {
                $viewLinter->deleteLintableCopy();
            }

        }

        $lintingDuration = round(microtime(true) - $startTime, 2);
        $progressBar->finish();

        $this->finishLintingOutput($results,  $outputAsJson,  $lintingDuration);
    }


}