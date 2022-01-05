<?php

namespace Beyondcode\LaravelProseLinter\Tests;

use Orchestra\Testbench\TestCase;
use Beyondcode\LaravelProseLinter\LaravelProseLinterServiceProvider;
use Beyondcode\LaravelProseLinter\Styles\Vale;
use Illuminate\Support\Facades\Config;

class LintTranslationCommandTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            LaravelProseLinterServiceProvider::class,
        ];
    }

    /** @test */
    public function it_does_lint_specific_namespace_with_hints()
    {
        $this->artisan('lint:translation auth')
            ->expectsTable(
                ['Key', 'Line', 'Position', 'Message', 'Severity', 'Condition'],
                [
                    ['auth.password', 1, 23, "Try to avoid using 'is'.", 'suggestion', 'write-good.E-Prime'],
                    ['auth.throttle', 1, 5, "'many' is a weasel word!", 'warning', 'write-good.Weasel'],
                ]
            );
    }

    /** @test */
    public function it_does_lint_specific_namespace_with_no_hints()
    {
        Config::set('linter.styles', [Vale::class]);

        $this->artisan('lint:translation passwords')
            ->expectsOutput('✅ No errors, warnings or suggestions found.');
    }

    /** @test */
    public function it_does_lint_specific_namespace_and_writes_json_output()
    {
        $this->artisan('lint:translation auth --json')
            ->expectsOutput('2 linting hints were found.')
            ->expectsOutput('For detail, check results in file');
    }

    /** @test */
    public function it_does_not_lint_nonexistent_namespace()
    {
        $this->artisan('lint:translation idonotexist')
            ->expectsOutput('(idonotexist) Unexpected error.');
    }

    /** @test */
    public function it_does_not_lint_nonexistent_namespace_verbose()
    {
        $this->artisan('lint:translation idonotexist --verbose')
            ->expectsOutput('(idonotexist) Unexpected error.')
            ->expectsOutput('No translations found.');
    }
}
