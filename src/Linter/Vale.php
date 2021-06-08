<?php

namespace Beyondcode\LaravelProseLinter\Linter;

use Exception;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;
use Beyondcode\LaravelProseLinter\Exceptions\LinterException;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * Class Vale
 * @package Beyondcode\LaravelProseLinter\Linter
 */
class Vale
{
    /**
     * @var string
     */
    protected string $valePath;

    /**
     * Vale constructor.
     */
    public function __construct()
    {
        $this->valePath = __DIR__ . "/../../bin/vale-ai";
        $this->writeValeIni();
    }

    /**
     * @param $textToLint
     * @param null $textIdentifier
     * @throws LinterException
     */
    public function lintString($textToLint, $textIdentifier = null)
    {
        if (!is_string($textToLint)) {
            return; // TODO
        }

        $process = Process::fromShellCommandline(
            './vale --output=JSON --ext=".md" "' . $textToLint . '"'
        );
        $process->setWorkingDirectory($this->valePath);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $result = json_decode($process->getOutput(), true);

        if (!empty($result)) {
            throw LinterException::withResult($result, $textIdentifier ?? "Text");
        } elseif ($result === null || !is_array($result)) {
            throw new LinterException("Invalid vale output: " . print_r($process->getOutput(), true));
        }
    }

    /**
     * @param $filePath
     * @param $textIdentifier
     * @throws LinterException
     */
    public function lintFile($filePath, $textIdentifier)
    {
        $process = Process::fromShellCommandline(
            './vale --output=JSON ' . $filePath
        );

        $process->setWorkingDirectory($this->valePath);

        $process->run();

        $result = json_decode($process->getOutput(), true);

        if (!empty($result)) {
            throw LinterException::withResult($result, $textIdentifier);
        } elseif ($result === null || !is_array($result)) {
            throw new Exception("Invalid vale output: " . print_r($process->getOutput(), true));
        }
    }

    /**
     * Build .vale.ini dynamically based on the configuration
     */
    protected function getAppliedStyles()
    {
        $configuredStyles = config('linter.styles', [\Beyondcode\LaravelProseLinter\Styles\Vale::class]);

        if (count($configuredStyles) == 0) {
            throw new Exception("No styles defined. Please check your config (linter.styles)!");
        }

        $styles = [];
        foreach ($configuredStyles as $configuredStyle) {
            $styleClass = new $configuredStyle();
            $styles[] = $styleClass->getStyleDirectoryName();
        }

        return implode(",", $styles);
    }

    /**
     *
     */
    private function writeStyles()
    {
        $stylePath = $this->valePath . "/styles";

        // clear temporary vale style directory
        File::deleteDirectory($stylePath);

        // copy resources from application styles if existing
        if (File::exists(resource_path('lang/vendor/laravel-prose-linter'))) {
            File::copyDirectory(
                resource_path('lang/vendor/laravel-prose-linter'),
                $stylePath
            );
        } else {
            // copy resources from default
            File::copyDirectory(__DIR__ . '/../../resources/styles', $stylePath);
        }
    }

    /**
     * Create .vale.ini during runtime
     */
    public function writeValeIni()
    {
        $appliedStyles = $this->getAppliedStyles();

        $this->writeStyles();

        $valeIni = "
StylesPath = styles
[*.{html,md}]
BasedOnStyles = {$appliedStyles}
";
        File::put($this->valePath . "/.vale.ini", $valeIni);
    }


}