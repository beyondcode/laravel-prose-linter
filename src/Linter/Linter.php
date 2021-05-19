<?php

namespace Beyondcode\LaravelProseLinter\Linter;

abstract class Linter
{
    protected array $results;
    protected string $valePath;

    public function __construct()
    {
        $this->valePath = base_path("vendor/beyondcode/laravel-prose-linter/src/vale-ai/bin");
    }

    public abstract function lintAll();

    public abstract function getResults();

    public abstract function hasErrors(): bool;


}
