---
title: Installation
order: 1
---
# Installation

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
