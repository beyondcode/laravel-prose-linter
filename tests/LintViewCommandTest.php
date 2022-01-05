<?php

namespace Beyondcode\LaravelProseLinter\Tests;

use Beyondcode\LaravelProseLinter\LaravelProseLinterServiceProvider;
use Beyondcode\LaravelProseLinter\Styles\WriteGood;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Orchestra\Testbench\TestCase;

class LintViewCommandTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            LaravelProseLinterServiceProvider::class,
        ];
    }

    /** @test */
    public function it_does_ask_for_confirmation_for_all_blade_templates()
    {
        $this->artisan('lint:blade')
            ->expectsConfirmation('Are you sure you want to lint all blade files in your application?', 'no')
            ->expectsOutput('❌ Linting aborted.')
            ->assertExitCode(1);

        $this->artisan('lint:blade')
            ->expectsConfirmation('Are you sure you want to lint all blade files in your application?', 'yes')
            ->expectsOutput('Linting all blade templates');
    }

    /** @test */
    public function it_aborts_command_with_invalid_parameters()
    {
        $this->artisan('lint:blade singlebladefile --exclude=directory')
            ->expectsOutput('Invalid parameters. Please provide either a single template key to lint or directories to exclude or no further options to lint all blade templates.')
            ->assertExitCode(2);
    }

    /** @test */
    public function it_lints_specific_blade_template()
    {
        Config::set('linter.styles', [WriteGood::class]);

        File::put(resource_path('views/temporary.blade.php'), '<html><body>I am a blade test file with some many words in it.</body></html>');

        $this->artisan('lint:blade temporary')
            ->expectsOutput("Linting single blade template with key 'temporary'.")
            ->expectsTable(
                ['Key', 'Line', 'Position', 'Message', 'Severity', 'Condition'],
                [
                    ['temporary', 1, 15, "Try to avoid using 'am'.", 'suggestion', 'write-good.E-Prime'],
                    ['temporary', 1, 46, "'many' is a weasel word!", 'warning', 'write-good.Weasel'],
                ]
            );
    }

    /** @test */
    public function it_does_not_lint_nonexistent_blade_template()
    {
        $this->artisan('lint:blade idonotexist')
            ->expectsOutput('(idonotexist) Unexpected error.');
    }

    /** @test */
    public function it_does_not_lint_nonexistent_blade_template_verbose()
    {
        $this->artisan('lint:blade idonotexist --verbose')
            ->expectsOutput('(idonotexist) Unexpected error.')
            ->expectsOutput('View [idonotexist] not found.');
    }
}
