<?php

namespace Beyondcode\LaravelProseLinter\Linter;

use Illuminate\Support\Facades\File;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use Symfony\Component\Process\Process;

class ViewLinter extends Linter
{
    public function lintAll()
    {
        $this->lintBladeTemplate("");
    }

    public function lintBladeTemplate($templateKey)
    {

        $process = Process::fromShellCommandline(
            'mkdir tmp'
        );
        $process->setWorkingDirectory($this->valePath);
        $process->run();

        $process = Process::fromShellCommandline(
            'mkdir results'
        );
        $process->setWorkingDirectory($this->valePath);
        $process->run();

        // copy a view to the tmp
        $viewPath = view('preview')->getPath();

        File::copy($viewPath, $this->valePath . "/tmp/preview.blade.html");

        $process = Process::fromShellCommandline(
            'ls'
        );
        $process->setWorkingDirectory($this->valePath . "/tmp/");
        $process->run();


        $process = Process::fromShellCommandline(
            'vale --output=JSON tmp'
        );
        $process->setWorkingDirectory($this->valePath);
        $process->run();

        $result = $process->getOutput();

        $process = Process::fromShellCommandline(
            'rm -rf tmp'
        );
        $process->setWorkingDirectory($this->valePath);
        $process->run();

        echo $result;

    }

    public function getResults()
    {
        // TODO: Implement getResults() method.
    }

    public function hasErrors(): bool
    {
        // TODO: Implement hasErrors() method.
    }


}