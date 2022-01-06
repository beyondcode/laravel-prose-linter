<?php

namespace Beyondcode\LaravelProseLinter\Linter;

class LintingResult
{
    /**
     * @var string
     */
    private string $textIdentifier;

    /**
     * @var LintingHint[]
     */
    private array $hints;

    /**
     * @param  string  $textIdentifier
     * @param  array  $results
     * @return LintingResult
     */
    public static function fromJsonOutput(string $textIdentifier, array $results): LintingResult
    {
        $lintingResult = new LintingResult();

        $lintingResult->textIdentifier = $textIdentifier;

        foreach ($results[array_key_first($results)] as $hint) {
            $lintingResult->hints[] = LintingHint::fromJson($hint);
        }

        return $lintingResult;
    }

    /**
     * @return string
     */
    public function getTextIdentifier(): string
    {
        return $this->textIdentifier;
    }

    /**
     * @return LintingHint[]
     */
    public function getHints(): array
    {
        return $this->hints;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $hints = [];
        foreach ($this->getHints() as $hint) {
            $hints[] = array_merge(
                [$this->getTextIdentifier()],
                $hint->toFlatArray());
        }

        return $hints;
    }
}
