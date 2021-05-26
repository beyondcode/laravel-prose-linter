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
        $this->writeValeIni();
    }

    public function lintString()
    {

    }

    public function lintFile($filePath, $textIdentifier)
    {
        $process = Process::fromShellCommandline(
            'vale --output=JSON ' . $filePath
        );
        $process->setWorkingDirectory($this->valePath);

        $process->run();

        $result = json_decode($process->getOutput(), true);

        if (!empty($result)) {
            throw LinterException::withResult($result, $textIdentifier);
        } elseif ($result === null || !is_array($result)) {
            throw new \Exception("Invalid vale output. " . print_r($process->getOutput(), true));
        }
    }

    /**
     * Build .vale.ini dynamically based on the configuration
     */
    protected function getAppliedStyles()
    {
        $configuredStyles = config('laravel-prose-linter.styles');

        $styles = [];
        foreach ($configuredStyles as $configuredStyle) {
            $styleClass = new $configuredStyle();
            $styles[] = $styleClass->getStyleDirectoryName();
        }

        return implode(",", $styles);
    }

    /**
     * Create .vale.ini during runtime
     */
    public function writeValeIni()
    {
        $appliedStyles = $this->getAppliedStyles();

        if (count($appliedStyles) == 0) {
            throw new \Exception("No styles defined. Please check your config (laravel-prose-linter.styles)!");
        }

        $valeIni = "
StylesPath = styles
[*.{html}]
BasedOnStyles = {$appliedStyles}
";
        File::put($this->valePath . "/.vale.ini", $valeIni);
    }

    public function restoreIni()
    {
        File::copy($this->valePath . "/.vale_default.ini", $this->valePath . "/.vale.ini");
    }


}