<?php

namespace Beyondcode\LaravelProseLinter\Linter;

use Beyondcode\LaravelProseLinter\Exceptions\LinterException;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ViewLinter extends Linter
{
    public function lintAll()
    {
        // collect all blade keys
        $this->results = [];
        $templates = ['preview'];
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