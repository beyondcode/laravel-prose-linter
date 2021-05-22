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

    public function toArray(): array
    {

        $hints = [];
        foreach ($this->getHints() as $hint) {
            $hints[] = $hint->toArray();
        }

        return [
            $this->getTextIdentifier() => $hints
        ];

    }
}