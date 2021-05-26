<?php

namespace Beyondcode\LaravelProseLinter\Linter;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use Beyondcode\LaravelProseLinter\Exceptions\LinterException;
use Symfony\Component\Process\Exception\ProcessFailedException;

class TranslationLinter extends Linter
{

    public function getTranslationFiles()
    {
        $languageDirectory = resource_path("lang/en");
        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($languageDirectory));

        $translationFiles = new Collection();

        # collect blade files recursively
        $it->rewind();
        while ($it->valid()) {

            if (!$it->isDot()) {
                $translationFiles->add($it->key());
            }

            $it->next();
        }

        # extract namespaces
        $namespaces = $translationFiles->map(function ($file) {
            if (Str::startsWith($file, ".")) return false;
            $fileName = Str::afterLast($file, "lang/en/");

            return Str::before($fileName, ".php");
        });

        return $namespaces->toArray();
    }

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
                $errors[] = $linterException->getResult();
            } catch (ProcessFailedException $processFailedException) {
                break; // todo
            }
        }

        return $errors;
    }

    public function lintSingleTranslation(string $translationKey, string $translationText)
    {

        $process = Process::fromShellCommandline(
            'vale --output=JSON --ext=".md" "' . $translationText . '"'
        );
        $process->setWorkingDirectory($this->valePath);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $result = json_decode($process->getOutput(), true);

        if (!empty($result)) {
            throw LinterException::withResult($result, $translationKey);
        } elseif ($result === null || !is_array($result)) {
            throw new LinterException("Invalid vale output.");
        }
    }


}
