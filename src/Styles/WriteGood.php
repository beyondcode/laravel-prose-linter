<?php

namespace Beyondcode\LaravelProseLinter\Styles;

class WriteGood implements StyleInterface
{
    /**
     * @return string
     */
    public static function getStyleDirectoryName(): string
    {
        return 'write-good';
    }
}
