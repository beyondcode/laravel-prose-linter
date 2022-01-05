<?php

namespace Beyondcode\LaravelProseLinter\Tests;

use Beyondcode\LaravelProseLinter\LaravelProseLinterServiceProvider;
use Beyondcode\LaravelProseLinter\Linter\TranslationLinter;
use Beyondcode\LaravelProseLinter\Styles\Vale;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Orchestra\Testbench\TestCase;

class TranslationLinterTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            LaravelProseLinterServiceProvider::class,
        ];
    }

    /** @test */
    public function it_returns_translation_files()
    {
        $linter = new TranslationLinter();

        $files = $linter->getTranslationFiles();

        $this->assertCount(4, $files);
        $this->assertContains('passwords', $files);
        $this->assertContains('auth', $files);
        $this->assertContains('pagination', $files);
        $this->assertContains('validation', $files);
    }

    /** @test */
    public function it_lints_single_translation()
    {
        $linter = new TranslationLinter();

        $result = $linter->lintSingleTranslation('auth.throttle', 'Too many login attempts. Please try again in :seconds seconds')[0];

        $this->assertIsArray($result);
        $this->assertContains('auth.throttle', $result);
        $this->assertContains("'many' is a weasel word!", $result);
        $this->assertContains('warning', $result);
        $this->assertContains('write-good.Weasel', $result);
    }

    /** @test */
    public function it_lints_single_translation_with_custom_styles()
    {
        Artisan::call('vendor:publish --tag=linting-config');
        Artisan::call('vendor:publish --tag=linting-styles');

        Config::set('linter.styles', [Vale::class]);

        $linter = new TranslationLinter();
        $result = $linter->lintSingleTranslation('auth.throttle', 'Too FIXME many login attempts. Please try again in :seconds seconds')[0];

        $this->assertIsArray($result);
        $this->assertContains('auth.throttle', $result);
        $this->assertContains("'FIXME' left in text", $result);
        $this->assertContains('suggestion', $result);
        $this->assertContains('vale.Annotations', $result);
    }
}
