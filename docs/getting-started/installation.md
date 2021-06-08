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