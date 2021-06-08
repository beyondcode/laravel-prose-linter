<?php

namespace Beyondcode\LaravelProseLinter\Linter;

use Illuminate\Support\Str;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Illuminate\Support\Collection;
use Symfony\Component\Process\Process;
use Beyondcode\LaravelProseLinter\Exceptions\LinterException;
use Symfony\Component\Process\Exception\ProcessFailedException;

class TranslationLinter extends Vale
{

    public function getTranslationFiles()
    {
        $languageDirectory = resource_path("lang/en");
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($languageDirectory));

        $translationFiles = new Collection();

        // collect blade files recursively
        $it->rewind();

        while ($it->valid()) {
            if (!$it->isDot()) {
                $translationFiles->add($it->key());
            }

            $it->next();
        }

        // extract namespaces
        $namespaces = $translationFiles->map(function ($file) {
            if (Str::startsWith($file, ".")) {
                return false;
            }

            $fileName = Str::afterLast($file, "lang/en/");

            return Str::before($fileName, ".php");
        });

        return $namespaces->toArray();
    }

    public function readTranslationArray(string $namespace)
    {
        // TODO flatten, e.g. validation
        return __($namespace);
    }

    public function lintNamespace(string $namespace): array
    {
        $translations = $this->readTranslationArray($namespace);

        $results = [];
        foreach ($translations as $translationKey => $translationText) {
            try {
                $this->lintString($translationText, "{$namespace}.{$translationKey}");
            } catch (LinterException $linterException) {
                $results[] = $linterException->getResult()->toArray();
            } catch (ProcessFailedException $processFailedException) {
                break; // todo
            }
        }

        return $results;
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
            throw new LinterException("Invalid vale output: " . print_r($process->getOutput(), true));
        }
    }


}
