---
title: Publishing and configuring styles
order: 2
---

# Publishing and configuring styles

Let's assume you want to use a style guide that implements the »Google Developer Documentation Style Guide« because you have documentation text in some blade templates.

First you've got to publish the linting styles as well as the linting configuration like explained in [Installation](docs/laravel-prose-linter/getting-started/installation). To integrate this style into your application, copy the `Google` directory of the [styles' repository](https://github.com/errata-ai/Google) to `resources/lang/vendor/laravel-prose-linter`.

Next up, create a class for your style in your Laravel app that implements the `Beyondcode\LaravelProseLinter\Styles\StyleInterface`:

```php
namespace App\Library\LaravelProseLinter;

use Beyondcode\LaravelProseLinter\Styles\StyleInterface;

class GoogleDeveloperDocumentationStyle implements StyleInterface
{

    public static function getStyleDirectoryName(): string
    {
        return 'Google';
    }

}
```

The interface implements the method `getStyleDirectoryName()` that has to return the name of the directory which contains the styles' linting rules, in this case it's `Google`.

To include the style in your linting, add it to the `styles` array in the `config/linter.php` file:

```php
/*
 * Customize the Vale styles used by the linter.
 */
return [
    'styles' => [
        App\Library\LaravelProseLinter\GoogleDeveloperDocumentationStyle::class
    ]

];
```

Lint a blade template or translation of your choice to see the results of this style:

```bash
~ php artisan lint:blade docs
Linting single blade template with key 'docs'.
🗣  Start linting ...
 1/1 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%
+---------+------+----------+------------------------------------------------------------------------------------------------+----------+---------------------------+
| Key     | Line | Position | Message                                                                                        | Severity | Condition                 |
+---------+------+----------+------------------------------------------------------------------------------------------------+----------+---------------------------+
| preview | 11   | 41       | 'Package Development' should use sentence-style capitalization.                                | warning  | Google.Headings           |
| preview | 75   | 141      | In general, don't use an ellipsis.                                                             | warning  | Google.Ellipses           |
| preview | 78   | 144      | Don't use exclamation points in text.                                                          | error    | Google.Exclamation        |
[...]
| preview | 282  | 164      | Don't put a period at the end of a heading.                                                    | warning  | Google.HeadingPunctuation |
+---------+------+----------+------------------------------------------------------------------------------------------------+----------+---------------------------+
45 linting hints were found.
Applied styles: GoogleDeveloperDocumentationStyle
🏁 Finished linting in 0.22 seconds.
```