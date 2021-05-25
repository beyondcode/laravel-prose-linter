<?php

namespace Beyondcode\LaravelProseLinter\Linter;


use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class LintingHint
{

    private string $libraryCheck;
    private int $line;
    private int $position;
    private string $message;
    private string $severity;

    public function __construct(string $libraryCheck, int $line, int $position, string $message, string $severity)
    {
        $this->libraryCheck = $libraryCheck;
        $this->line = $line;
        $this->position = $position;
        $this->message = $message;
        $this->severity = $severity;
    }

    public static function fromJson(array $result): LintingHint
    {
        $hintData = $result;

        $hint = new LintingHint(
            $hintData["Check"],
            $hintData['Line'],
            $hintData['Span'][0],
            $hintData["Message"],
            $hintData["Severity"]
        );

        return $hint;
    }

    public function toCLIOutput()
    {
        return "[{$this->severity}] {$this->libraryCheck} at position {$this->line}, {$this->position}: {$this->message}";
    }

    public function toArray()
    {
        return [
            "line" => $this->line,
            "position" => $this->position,
            "message" => $this->message,
            "severity" => $this->severity,
            "libraryCheck" => $this->libraryCheck,
        ];
    }


    public function toFlatArray()
    {
        return [
            $this->line,
            $this->position,
            $this->message,
            $this->severity,
            $this->libraryCheck,
        ];

    }
}