<?php

namespace Beyondcode\LaravelProseLinter\Linter;

use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Symfony\Component\Process\Process;
use Beyondcode\LaravelProseLinter\Exceptions\LinterException;

class TranslationLinter extends Linter
{

    public function lintAll()
    {
        // toDo all without namespace
        $namespaceTranslations = ["auth" => $this->readTranslationArray("auth")];

        $this->results = [];
        foreach ($namespaceTranslations as $namespaceKey => $translations) {

            $lintingResult = $this->lintTranslations($translations);

            $this->results[$namespaceKey] = $lintingResult;
        }

        return $this->results;
    }

    public function readTranslationArray(string $namespace)
    {
        return __($namespace);
    }

    public function lintTranslations(array $translations): array
    {
        $errors = [];
        foreach ($translations as $translationKey => $translationText) {
            try {
                $this->lintSingleTranslation($translationKey, $translationText);
            } catch (LinterException $linterException) {
                $errors[$translationKey] = $linterException->getHint();
            } catch (ProcessFailedException $processFailedException) {
                break; // todo
            }
        }

        return $errors;
    }

    public function lintSingleTranslation(string $translationKey, string $translationText)
    {

        $process = Process::fromShellCommandline(
            'vale --output=line --ext=".md" "' . $translationText . '"'
        );
        $process->setWorkingDirectory($this->valePath);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output = $process->getOutput();

        if (!empty($output))
            throw LinterException::withHint($output, $translationKey);
    }

    public function getResults()
    {
        return $this->results;
    }

    public function hasErrors(): bool
    {
        if(count($this->results) == 0) {
            return false;
        }
        return true;
    }


}
