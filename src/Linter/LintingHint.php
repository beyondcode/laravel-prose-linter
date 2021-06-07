<?php

namespace Beyondcode\LaravelProseLinter\Linter;


use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class LintingHint
{

    /**
     * @var string Library condition that produced the hint, e.g. "write-good.TooWordy"
     */
    private string $libraryCheck;

    /**
     * @var int
     */
    private int $line;

    /**
     * @var int
     */
    private int $position;

    /**
     * @var string
     */
    private string $message;

    /**
     * @var string
     */
    private string $severity;

    /**
     * @param string $libraryCheck
     * @param int $line
     * @param int $position
     * @param string $message
     * @param string $severity
     */
    public function __construct(string $libraryCheck, int $line, int $position, string $message, string $severity)
    {
        $this->libraryCheck = $libraryCheck;
        $this->line = $line;
        $this->position = $position;
        $this->message = $message;
        $this->severity = $severity;
    }

    /**
     * @param array $result
     * @return LintingHint
     */
    public static function fromJson(array $result): LintingHint
    {
        $hintData = $result;

        return new LintingHint(
            $hintData["Check"],
            $hintData['Line'],
            $hintData['Span'][0],
            $hintData["Message"],
            $hintData["Severity"]
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            "line" => $this->line,
            "position" => $this->position,
            "message" => $this->message,
            "severity" => $this->severity,
            "libraryCheck" => $this->libraryCheck,
        ];
    }


    /**
     * @return array
     */
    public function toFlatArray(): array
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