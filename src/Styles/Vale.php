<?php

namespace Beyondcode\LaravelProseLinter\Styles;

// TODO this is an own style, maybe rename this
class Vale implements StyleInterface
{

    public static function getStyleDirectoryName(): string
    {
        return 'vale';
    }
}
