<?php

namespace Beyondcode\LaravelProseLinter\Linter;

use Beyondcode\LaravelProseLinter\Exceptions\LinterException;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Process\Exception\ProcessFailedException;

class TranslationLinter extends Vale
{
    private array $lintingResults = [];

    /**
     * @return array
     */
    public function getTranslationFiles(): array
    {
        $languageDirectory = resource_path("lang{$this->directorySeparator}en");
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($languageDirectory));

        $translationFiles = new Collection();

        // collect blade files recursively
        $it->rewind();

        while ($it->valid()) {
            if (! $it->isDot()) {
                $translationFiles->add($it->key());
            }

            $it->next();
        }
        // extract namespaces
        $namespaces = $translationFiles->map(function ($file) {
            if (Str::startsWith($file, '.')) {
                return false;
            }

            $fileName = Str::afterLast($file, "lang{$this->directorySeparator}en{$this->directorySeparator}");

            return Str::before($fileName, '.php');
        });

        return $namespaces->toArray();
    }

    /**
     * @param  string  $namespace
     * @return array|string
     */
    public function readTranslationArray(string $namespace)
    {
        // TODO flatten, e.g. validation
        return __($namespace);
    }


    /**
     * @param  string  $namespace
     * @return array
     *
     * @throws LinterException
     */
    public function lintNamespace(string $namespace): array
    {
        $translations = $this->readTranslationArray($namespace);

        if (! is_array($translations)) {
            throw new LinterException('No translations found.');
        }

        $this->lintingResults = [];
        try {
            $this->lintTranslationArray($translations, $namespace);
        } catch (ProcessFailedException $processFailedException) {
            // toDo
        }

        return $this->lintingResults;
    }

    private function lintTranslationArray($translations, $parentKey = null)
    {
        foreach ($translations as $translationKey => $translationText) {
            if (is_array($translationText)) {
                $fullKey = $this->translationKey($translationKey, $parentKey);
                $this->lintTranslationArray($translationText, $fullKey);
                continue;
            }

            $result = $this->lintString(
                $translationText,
                $this->translationKey($translationKey, $parentKey)
            );

            if ($result === null) {
                continue;
            }

            $this->lintingResults[] = $result;
        }
    }

    private function translationKey($translationKey, $parentKey = null)
    {
        return $parentKey ? $parentKey.'.'.$translationKey : $translationKey;
    }

    /**
     * @param  string  $translationKey
     * @param  string  $translationText
     *
     * @throws LinterException
     */
    public function lintSingleTranslation(string $translationKey, string $translationText)
    {
        return $this->lintString($translationText, $translationKey);
    }
}
