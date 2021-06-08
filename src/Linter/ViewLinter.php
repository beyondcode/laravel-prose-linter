<?php

namespace Beyondcode\LaravelProseLinter\Linter;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Symfony\Component\Finder\Finder;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;

class ViewLinter extends Vale
{

    /**
     * @param $excludedDirectories
     * @return Collection
     */
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


    /**
     *
     */
    public function deleteLintableCopy()
    {
        File::deleteDirectory($this->valePath . "/tmp");
    }

    /**
     * @param $templateKey
     * @return string
     */
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