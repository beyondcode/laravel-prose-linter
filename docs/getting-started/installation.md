---
title: Installation
order: 1
---

Syntax-aware proofreading for your Laravel application.

The Laravel Prose Linter helps you to polish the texts of your Laravel application. Let it check your translations and even your blade templates for typos, slang and get suggestions for a better writing style depending on which prose style you choose.

We recommend to take a quick glimpse at the [errata-ai/vale](https://docs.errata.ai/vale/about) package to learn what prose linting is all about.

# Installation

## System Requirements
This package requires PHP 8.0 or higher.

You can install the package via composer:

```bash
~ composer require beyondcode/laravel-prose-linter
```

If you want to customize the styles used by the linter (see **here**), publish the config and the style assets:

```bash
~ php artisan vendor:publish --tag=linting-config
~ php artisan vendor:publish --tag=linting-styles
```


With that, you're ready to lint!
```bash
~ php artisan lint:translation auth validation
🗣  Start linting ...
 2/2 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%
+---------------------------------+------+----------+--------------------------------------------------------------------+----------+---------------------+
| Key.                            | Line | Position | Message                                                            | Severity | Condition           |
+---------------------------------+------+----------+--------------------------------------------------------------------+----------+---------------------+
| auth.throttle                   | 1    | 5        | 'many' is a weasel word!                                           | warning  | write-good.Weasel   |
| validation.accepted             | 1    | 21       | 'be accepted' may be passive voice. Use active voice if you can.   | warning  | write-good.Passive  |
[...]
+---------------------------------+------+----------+--------------------------------------------------------------------+----------+---------------------+
17 linting hints were found.
Applied styles: WriteGood, Vale
🏁 Finished linting in 8 seconds.
```
