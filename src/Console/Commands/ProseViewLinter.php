<?php

namespace Beyondcode\LaravelProseLinter\Console\Commands;

use Illuminate\Console\Command;
use Beyondcode\LaravelProseLinter\Linter\ViewLinter;
use Illuminate\Support\Facades\File;

class ProseViewLinter extends Command
{
    protected $signature = 'lint:blade {--exclude=? : directories to exclude in format dir1,dir2,dir3 } {--json}';

    protected $description = "Lints blade templates.";

    public function handle()
    {
        $this->info("🗣  Start linting blade templates");
        $startTime = microtime(true);

        $linter = new ViewLinter();

        $outputAsJson = $this->option("json") ? true : false;
        $jsonOutput = [];

        if($this->option("exclude") !== false) {
            $linter
                ->excludes(explode(",", $this->option("exclude")));
        }

        $linter->lintAll();

        if ($linter->hasErrors()) {
            // go through namespaces
            foreach ($linter->getResults() as $namespaceKey => $lintingResult) {

                if ($outputAsJson) {
                    $jsonOutput[] = $lintingResult->toArray();
                } else {
                    $this->newLine();
                    $this->warn("{$lintingResult->getTextIdentifier()}.blade.php:");

                    // go through hints in translation linting result
                    foreach ($lintingResult->getHints() as $hint) {
                        $this->line($hint->toCliOutput());
                    }
                }

            }

            // write json file with results to storage
            if($outputAsJson) {
                $filePath = storage_path("linting_result_".date("Y-m-d-H-i-s").".json");
                File::put($filePath, json_encode($jsonOutput));
                $this->warn("Linting errors were found. For detail, check results in file");
                $this->warn($filePath);
            }
        } else {
            $this->info("✅ No errors, warnings or suggestions found.");
        }


        $lintingDuration = round(microtime(true) - $startTime, 2);
        $this->info("🏁 Finished linting in {$lintingDuration} seconds.");
    }

}