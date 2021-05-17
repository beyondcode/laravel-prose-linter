<?php

namespace Beyondcode\LaravelProseLinter\Exceptions;

use Beyondcode\LaravelProseLinter\Linter\LintingHint;

class LinterException extends \Exception
{
    public LintingHint $hint;

    public static function withHint(string $output, string $translationKey) {
        $e = new LinterException("Linting errors were found");

        $e->hint = LintingHint::fromCLIOutput($output, $translationKey);

        return $e;
    }

    public function getHint(): LintingHint {
        return $this->hint;
    }

}
