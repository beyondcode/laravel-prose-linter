# Evaluating the results

## CLI

The linting results of blade templates and translations are both printed as a table in the CLI and may look like this:

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

The result gives you a short overview which translations or blade templates were linted, how long linting took and which styles were applied. The table lists all linting hints with translation or template key and a position to find the respective sentence or word in the text.

## JSON

If you provide the `--json` flag in the command as stated above, the results file can be found in your applications `storage` folder. It will look a bit like this:

```bash
[
  [
    "auth.throttle",
    1,
    5,
    "'many' is a weasel word!",
    "warning",
    "write-good.Weasel"
  ]
]
```

The order of the array elements is the same as in the CLI output: Translation or template key, line, position, linting hint message, severity and the condition of the library that produced the hint.