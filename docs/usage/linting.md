# Linting

## Blade templates

To lint all blade templates of your project, call the following command from your project root:

```bash
~ php artisan lint:blade
```

Linting all templates of your app can take a while, so it will ask you for confirmation before it starts.

As an alternative, you can lint either a single blade template by providing the template key just like you would inside laravel:

```bash
~ php artisan lint:blade auth.login
```

Or you exclude one or several view directories by using the `--exclude` option:

```bash
~ php artisan lint:blade --exclude=auth,vendor
```

## Translations

If you want to lint all translations of your application, open up your CLI in the Laravel project root and execute the following command:

```bash
~ php artisan lint:translation
```

This can be quite time-consuming for big applications, so if you want to restrict the linting to a certain namespace*, provide it as a parameter:

```bash
~ php artisan lint:translation auth
```

If you want to lint the translations of multiple namespaces, add them as further parameters:

```bash
~ php artisan lint:translation auth passwords pagination
```

* A translation namespace in Laravel is the name file of the file before the file extension where the translation array is returned. For example: The translations of the `passwords` namespace are located in `resources/lang/en/passwords.php` .

## Output

Instead of evaluating the results in the CLI as a table you for further processing or storing by appending a `--json` flag to the command:

```bash
~ php artisan lint:translation --json
```

The result file will be saved in the `storage` folder of your application.