<?php

namespace Beyondcode\LaravelProseLinter\Tests;

use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Artisan;
use Beyondcode\LaravelProseLinter\Linter\TranslationLinter;
use Beyondcode\LaravelProseLinter\Exceptions\LinterException;
use Beyondcode\LaravelProseLinter\LaravelProseLinterServiceProvider;

class TranslationLinterTest extends TestCase
{

    protected function getPackageProviders($app)
    {
        return [
            LaravelProseLinterServiceProvider::class
        ];
    }

    /** @test */
    public function it_returns_translation_files()
    {
        $linter = new TranslationLinter();

        $files = $linter->getTranslationFiles();

        $this->assertCount(4, $files);
        $this->assertContains("passwords", $files);
        $this->assertContains("auth", $files);
        $this->assertContains("pagination", $files);
        $this->assertContains("validation", $files);
    }

    /** @test */
    public function it_lints_single_translation()
    {
        $linter = new TranslationLinter();

        $this->expectException(LinterException::class);
        $linter->lintSingleTranslation("auth.throttle", "Too many login attempts. Please try again in :seconds seconds");
    }


    /** @test */
    public function it_lints_single_translation_with_custom_styles()
    {
        Artisan::call('vendor:publish --tag=config');
        Artisan::call('vendor:publish --tag=linting-styles');


        $linter = new TranslationLinter();

        $this->expectException(LinterException::class);
        $linter->lintSingleTranslation("auth.throttle", "Too many login attempts. Please try again in :seconds seconds");
    }

}
