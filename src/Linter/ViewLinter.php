<?php

namespace Beyondcode\LaravelProseLinter\Linter;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use Beyondcode\LaravelProseLinter\Exceptions\LinterException;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ViewLinter extends Linter
{
    protected array $excludes = [];


    public function lintAll()
    {
        // collect all blade keys
        $this->results = [];
        $templates = $this->listBlades()->toArray();

        foreach ($templates as $templateKey) {
            try {
                $this->lintBladeTemplate($templateKey);
            } catch (LinterException $linterException) {
                $this->results[] = $linterException->getResult();
            } catch (ProcessFailedException $processFailedException) {
                break; // todo
            }
        }

        return $this->results;
    }


    public function excludes(array $excludes)
    {
        $this->excludes = $excludes;
        return $this;
    }

    public function includes(array $includes)
    {
        $this->includes = $includes;
        return $this;
    }


    private function listBlades()
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
        $bladeTemplateKeys = $bladeTemplateFiles->map(function ($bladeTemplateFile) {
            if (Str::startsWith($bladeTemplateFile, ".")) return false;

            // get filename
            $bladePath = Str::afterLast($bladeTemplateFile, "views/");
            if (!empty($this->excludes) && Str::startsWith($bladePath, $this->excludes)) return false;

            // extract template key
            $bladePath = Str::before($bladePath, ".blade.php");

            // replace slashes with dots
            return Str::replace("/", ".", $bladePath);

        })->reject(function ($bladeTemplateFile) {
            return $bladeTemplateFile === false;
        });

        return $bladeTemplateKeys;
    }


    private function deleteLintableCopy($templateKey)
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

    public function lintBladeTemplate($templateKey)
    {
        $this->createLintableCopy($templateKey);

        $process = Process::fromShellCommandline(
            'vale --output=JSON tmp'
        );
        $process->setWorkingDirectory($this->valePath);
        $process->run();

        $this->deleteLintableCopy($templateKey);

        $result = json_decode($process->getOutput(), true);

        if (!empty($result)) {
            throw LinterException::withResult($result, $templateKey);
        } elseif ($result === null || !is_array($result)) {
            throw new LinterException("Invalid vale output.");
        }
    }
}