<?php

namespace Beyondcode\LaravelProseLinter\Linter;


use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class LintingHint
{

    private string $libraryCheck;
    private int $position;
    private string $message;
    private string $severity;

    public function __construct(string $libraryCheck, int $position, string $message, string $severity)
    {
        $this->libraryCheck = $libraryCheck;
        $this->position = $position;
        $this->message = $message;
        $this->severity = $severity;
    }

    public static function fromJson(array $result): LintingHint
    {
        $hintData = $result;

        $hint = new LintingHint(
            $hintData["Check"],
            $hintData["Span"][0],
            $hintData["Message"],
            $hintData["Severity"]
        );

        return $hint;
    }

    public function toCLIOutput()
    {
        return "[{$this->severity}] {$this->libraryCheck} at position {$this->position}: {$this->message}";
    }

    public function toArray() {
        return [
            "libraryCheck" => $this->libraryCheck,
            "position" => $this->position,
            "message" => $this->message,
            "severity" => $this->severity,
        ];

    }

}