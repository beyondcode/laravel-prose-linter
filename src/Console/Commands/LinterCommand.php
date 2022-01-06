<?php

namespace Beyondcode\LaravelProseLinter\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

abstract class LinterCommand extends Command
{
    /**
     * @param  array  $results
     * @param  bool  $outputAsJson
     * @param  float  $lintingDuration
     */
    protected function finishLintingOutput(array $results, bool $outputAsJson, float $lintingDuration)
    {
        $this->info(PHP_EOL);

        $totalHints = count($results);

        if ($totalHints > 0) {
            if ($outputAsJson) {
                $filePath = storage_path('linting_blade_result_'.date('Y-m-d-H-i-s').'.json');
                File::put($filePath, json_encode($results, JSON_UNESCAPED_SLASHES));

                $this->warn("$totalHints linting hints were found.");
                $this->warn('For detail, check results in file');
                $this->warn($filePath);
            } else {
                $this->table(
                    ['Key', 'Line', 'Position', 'Message', 'Severity', 'Condition'],
                    $results
                );
                $this->warn("$totalHints linting hints were found.");
            }
        } else {
            $this->info('✅ No errors, warnings or suggestions found.');
        }

        $this->info(
            'Applied styles: '.
            collect(config('linter.styles'))
                ->map(function ($style) {
                    return Str::afterLast($style, '\\');
                })
                ->implode(', ')
        );

        $this->info("🏁 Finished linting in $lintingDuration seconds.");
    }
}
