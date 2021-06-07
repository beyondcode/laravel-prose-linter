<?php

namespace Beyondcode\LaravelProseLinter\Linter;

abstract class Linter
{
    protected array $results;
    protected string $valePath;

    public function __construct()
    {
        $this->valePath = __DIR__ . "/../../bin/valet-ai";
    }

    public abstract function lintAll();


    public function getResults()
    {
        return $this->results;
    }

    public function hasErrors(): bool
    {
        return count($this->results) > 0;
    }



}
