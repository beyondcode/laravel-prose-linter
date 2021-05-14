<?php

namespace Beyondcode\LaravelProseLinter\Linter;

use Cassandra\Exception\UnpreparedException;

class Linter
{

    protected string $valePath;

    public function __construct()
    {
        $this->valePath = base_path("vendor/beyondcode/laravel-prose-linter/src/vale-ai/bin");
    }

    public function all() {
        throw new UnpreparedException();
    }


}
