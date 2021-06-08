<?php

namespace Beyondcode\LaravelProseLinter\Linter;

use Illuminate\Support\Str;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Illuminate\Support\Collection;
use Beyondcode\LaravelProseLinter\Exceptions\LinterException;
use Symfony\Component\Process\Exception\ProcessFailedException;

class TranslationLinter extends Vale
{

    /**
     * @return array
     */
    public function getTranslationFiles(): array
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

    /**
     * @param string $namespace
     * @return array|string
     */
    public function readTranslationArray(string $namespace)
    {
        // TODO flatten, e.g. validation
        return __($namespace);
    }

    /**
     * @param string $namespace
     * @return array
     * @throws LinterException
     */
    public function lintNamespace(string $namespace): array
    {
        $translations = $this->readTranslationArray($namespace);

        if (!is_array($translations)) {
            throw new LinterException("No translations found.");
        }

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

    /**
     * @param string $translationKey
     * @param string $translationText
     * @throws LinterException
     */
    public function lintSingleTranslation(string $translationKey, string $translationText)
    {
        $this->lintString($translationText, $translationKey);
    }


}
