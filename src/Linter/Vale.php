<?php

namespace Beyondcode\LaravelProseLinter\Linter;

use Beyondcode\LaravelProseLinter\Exceptions\LinterException;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class Vale
{
    protected array $results;
    protected string $valePath;

    public function __construct()
    {
        $this->valePath = base_path("vendor/beyondcode/laravel-prose-linter/src/vale-ai/bin");
    }

    public function lintString()
    {

    }

    public function lintFile($filePath, $textIdentifier = null)
    {
        $process = Process::fromShellCommandline(
            'vale --output=JSON tmp'
        );
        $process->setWorkingDirectory($this->valePath);
        $process->run();

        $result = json_decode($process->getOutput(), true);

        if (!empty($result)) {
            throw LinterException::withResult($result, $textIdentifier ?? $filePath);
        } elseif ($result === null || !is_array($result)) {
            throw new LinterException("Invalid vale output.");
        }
    }

    private function deleteLintableCopy()
    {
        $process = Process::fromShellCommandline(
            'rm -rf tmp'
        );
        $process->setWorkingDirectory($this->valePath);
        $process->run();
    }

    private function createLintableCopy($templateKey)
    {

        $process = Process::fromShellCommandline(
            'mkdir tmp'
        );
        $process->setWorkingDirectory($this->valePath);
        $process->run();

        // copy a view to the tmp
        $viewPath = view($templateKey)->getPath();

        File::copy($viewPath, $this->valePath . "/tmp/{$templateKey}.blade.html");

    }

    /**
     * Build .vale.ini dynamically based on the configuration
     */
    public function buildBasedOnStyles() {
        $configuredStyles = config('laravel-prose-linter.styles');

        $buildBasedOnStyles = "";


        $styles = [];
        foreach($configuredStyles as $configuredStyle) {
            $styleClass = new $configuredStyle();
            $styles[] = $styleClass->getStyleDirectoryName();
        }

        dd($styles);

    }


}