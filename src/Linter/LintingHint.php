<?php

namespace Beyondcode\LaravelProseLinter\Linter;


use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class LintingHint
{
    private string $textIdentifier;

    private array $hints;

    public static function fromCLIOutput(string $output, string $translationKey) {
        $hint = new LintingHint();

        $hint->setTextIdentifier($translationKey);

        // parse CLI output
        $allMessages = Str::of($output)->explode("stdin.md:")->toArray();
        $allValidMessages = Arr::where($allMessages, function ($value, $key) {
            return !empty($value);
        });

        foreach ($allValidMessages as $message) {
            // remove first line pos
            $result = Str::substr($message, 2);

            // determine column error position given by vale
            $pos = Str::before($result, ":");

            // determine hints from vale
            $message = Str::after($result, ":");

            $hint->addHint("At position {$pos}: {$message}");
        }

        return $hint;
    }

    public function toCLIOutput() {
        return implode("", $this->hints);
    }


    /**
     * @return string
     */
    public function getTextIdentifier(): string
    {
        return $this->textIdentifier;
    }

    /**
     * @param string $textIdentifier
     * @return LintingHint
     */
    public function setTextIdentifier(string $textIdentifier): LintingHint
    {
        $this->textIdentifier = $textIdentifier;
        return $this;
    }

    /**
     * @return array
     */
    public function getHints(): array
    {
        return $this->hints;
    }

    /**
     * @param array $hint
     * @return LintingHint
     */
    public function addHint(string $hint): LintingHint
    {
        $this->hints[] = $hint;
        return $this;
    }


}