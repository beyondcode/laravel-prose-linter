<?php

namespace Beyondcode\LaravelProseLinter\Linter;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use Beyondcode\LaravelProseLinter\Exceptions\LinterException;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ViewLinter extends Vale
{

    public function readBladeKeys($excludedDirectories)
    {

        $viewsDirectory = resource_path("views");
        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($viewsDirectory));

        $bladeTemplateFiles = new Collection();

        // collect blade files recursively
        $it->rewind();
        while ($it->valid()) {

            if (!$it->isDot()) {
                $bladeTemplateFiles->add($it->key());
            }

            $it->next();
        }

        // filter dot files and extract template keys
        $bladeTemplateKeys = $bladeTemplateFiles->map(function ($bladeTemplateFile) use ($excludedDirectories) {

            // check filename for dot files
            $fileName = Str::afterLast($bladeTemplateFile, "/");
            if (Str::startsWith($fileName, ".")) return false;

            // check for included / excluded directories
            $bladePath = Str::afterLast($bladeTemplateFile, "views/");
            if (!empty($excludedDirectories) && Str::startsWith($bladePath, $excludedDirectories)) return false;

            // extract template key
            $bladePath = Str::before($bladePath, ".blade.php");

            // replace slashes with dots
            return Str::replace("/", ".", $bladePath);

        })->reject(function ($bladeTemplateFile) {
            return $bladeTemplateFile === false;
        });

        return $bladeTemplateKeys;
    }


    public function deleteLintableCopy()
    {
        $process = Process::fromShellCommandline(
            'rm -rf tmp'
        );
        $process->setWorkingDirectory($this->valePath);
        $process->run();
    }

    public function createLintableCopy($templateKey): string
    {
        $process = Process::fromShellCommandline(
            'mkdir tmp'
        );
        $process->setWorkingDirectory($this->valePath);
        $process->run();

        // copy a view to the tmp
        $viewPath = view($templateKey)->getPath();

        $templateCopyPath = $this->valePath . "/tmp/{$templateKey}.blade.html";
        File::copy($viewPath, $templateCopyPath);

        return $templateCopyPath;
    }
}