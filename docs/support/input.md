---
title: Input
parent: Support
nav_order: 1
---

# Input Helper

Convenience methods for basic terminal input.

```php
use Ajaxray\AnsiKit\Support\Input;

$name = Input::line('Your name? [Anonymous] ', 'Anonymous');
$ok   = Input::confirm('Are you sure to proceed?', true);
$bio  = Input::multiline("Enter your bio. End with '---'", '---');

echo "Hi $name\n";
```

Notes
- Uses `readline` when available; falls back to `STDIN`.
- `confirm()` default controls the Enter key behavior.
