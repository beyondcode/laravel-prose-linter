---
title: Customization
order: 3
---

# Customization

A set of linting rules is called a *style*. The Laravel Prose Linter comes pre-defined with two styles: WriteGood and the default Vale style.

You can customize the styles by publishing the package config as described before and have a look at the `config/linter.php` file:

```php
/*
 * Customize the Vale styles used by the linter.
 */
return [
    'styles' => [
        \Beyondcode\LaravelProseLinter\Styles\WriteGood::class,
        \Beyondcode\LaravelProseLinter\Styles\Vale::class
    ]

];
```

There has to be at least one style applied for the linter to work. When you use multiple styles, please consider that some of the linting rules can conflict with each other.