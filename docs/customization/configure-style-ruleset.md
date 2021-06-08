---
title: Configure style ruleset
order: 1
---

# Customization

## Configure style ruleset

A set of linting rules is called a *style*. The Laravel Prose Linter comes pre-defined with two styles: WriteGood and the default Vale style.

You can customize the styles by publishing the package config as described in [Installation](docs/laravel-prose-linter/getting-started/installation) and have a look at the `config/linter.php` file:

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