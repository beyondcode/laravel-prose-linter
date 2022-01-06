<?php

namespace Beyondcode\LaravelProseLinter\Linter;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class ViewLinter extends Vale
{
    /**
     * @param $excludedDirectories
     * @return Collection
     */
    public function readBladeKeys($excludedDirectories): Collection
    {
        $viewFinder = (new Finder())
            ->ignoreDotFiles(true)
            ->in(resource_path('views'))
            ->exclude($excludedDirectories)
            ->name('*.blade.php');

        $bladeTemplateKeys = new Collection();

        /** @var SplFileInfo $viewFile */
        foreach ($viewFinder as $viewFile) {
            $relativePath = $viewFile->getRelativePath();
            if(!empty($relativePath)) {
                $relativePath = $relativePath . '.';
            }
            $viewName =  $relativePath .$viewFile->getBasename('.blade.php');

            $bladeTemplateKeys->add(str_replace('/', '.', $viewName));
        }

        return $bladeTemplateKeys;
    }

    public function deleteLintableCopy()
    {
        File::deleteDirectory($this->valePath.'/tmp');
    }

    /**
     * @param $templateKey
     * @return string
     */
    public function createLintableCopy($templateKey): string
    {
        File::makeDirectory($this->valePath.'/tmp');

        // copy a view to the tmp
        $viewPath = view($templateKey)->getPath();

        $templateCopyPath = $this->valePath."/tmp/{$templateKey}.blade.html";
        File::copy($viewPath, $templateCopyPath);

        return $templateCopyPath;
    }
}
