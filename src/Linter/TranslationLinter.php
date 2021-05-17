<?php

namespace Beyondcode\LaravelProseLinter\Linter;

use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Symfony\Component\Process\Process;
use Beyondcode\LaravelProseLinter\Exceptions\LinterException;

class TranslationLinter extends Linter
{

    public function all()
    {
        // toDo all without namespace
        $namespaceTranslations = ["auth" => $this->readTranslationArray("auth")];

        $results = [];
        foreach ($namespaceTranslations as $namespaceKey => $translations) {

            $lintingResult = $this->lintTranslations($translations);

            $results[$namespaceKey] = $lintingResult;
        }

        return $results;
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

}
