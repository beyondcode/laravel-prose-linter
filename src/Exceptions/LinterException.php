<?php

namespace Beyondcode\LaravelProseLinter\Exceptions;

use Exception;
use Beyondcode\LaravelProseLinter\Linter\LintingResult;

class LinterException extends Exception
{
    public LintingResult $result;

    /**
     * Creates a LinterException with a linting result & hints.
     *
     * @param array $output
     * @param string $textKey
     * @return LinterException
     */
    public static function withResult(array $output, string $textKey)
    {
        $e = new LinterException("Linting errors were found");

        $e->result = LintingResult::fromJsonOutput($textKey, $output);

        return $e;
    }

    /**
     * @return LintingResult
     */
    public function getResult(): LintingResult
    {
        return $this->result;
    }
}
