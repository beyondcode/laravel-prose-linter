<?php

namespace Beyondcode\LaravelProseLinter;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Beyondcode\LaravelProseLinter\Skeleton\SkeletonClass
 */
class LaravelProseLinterFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-prose-linter';
    }
}
