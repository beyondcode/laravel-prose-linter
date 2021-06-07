<?php

namespace Beyondcode\LaravelProseLinter\Linter;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Process;
use Beyondcode\LaravelProseLinter\Exceptions\LinterException;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ViewLinter extends Vale
{

    public function readBladeKeys($excludedDirectories)
    {
        $viewFinder = (new Finder())
            ->ignoreDotFiles(true)
            ->in(resource_path("views"))
            ->exclude($excludedDirectories)
            ->name('*.blade.php');

        $bladeTemplateKeys = new Collection();

        /** @var SplFileInfo $viewFile */
        foreach ($viewFinder as $viewFile) {
            $viewName = $viewFile->getRelativePath() . '.' . $viewFile->getBasename('.blade.php');

            $bladeTemplateKeys->add(Str::replace('/', '.', $viewName));
        }

        return $bladeTemplateKeys;
    }


    public function deleteLintableCopy()
    {
        File::deleteDirectory($this->valePath . "/tmp");
    }

    public function createLintableCopy($templateKey): string
    {
        File::makeDirectory($this->valePath . "/tmp");

        // copy a view to the tmp
        $viewPath = view($templateKey)->getPath();

        $templateCopyPath = $this->valePath . "/tmp/{$templateKey}.blade.html";
        File::copy($viewPath, $templateCopyPath);

        return $templateCopyPath;
    }
}