---
title: Keypress
parent: Support
nav_order: 3
---

# Keypress — Interactive Keyboard Input

Detect keyboard input in terminal apps using constants and helpers.

## Run the examples

```bash
php examples/keypress.php           # basic demos
php examples/keypress-advanced.php  # advanced sequences, modifiers
php examples/test-keypress.php      # simple test runner
```

## Basic usage

```php
use Ajaxray\AnsiKit\Support\Keypress;

$key = Keypress::listen();
if ($key === Keypress::KEY_UP) {
    echo "Up arrow pressed!";
}
```

## Common constants

```php
// Arrows & basics
Keypress::KEY_UP; Keypress::KEY_DOWN; Keypress::KEY_LEFT; Keypress::KEY_RIGHT;
Keypress::KEY_ENTER; Keypress::KEY_SPACE; Keypress::KEY_BACKSPACE; Keypress::KEY_TAB; Keypress::KEY_ESC;

// Navigation & function keys
Keypress::KEY_HOME; Keypress::KEY_END; Keypress::KEY_PAGE_UP; Keypress::KEY_PAGE_DOWN; Keypress::KEY_DELETE;
Keypress::KEY_F1; /* ... */ Keypress::KEY_F12;

// Ctrl combos
Keypress::KEY_CTRL_A; /* ... */ Keypress::KEY_CTRL_Z;
```

## Helpers

```php
$name = Keypress::getKeyName(Keypress::KEY_CTRL_C); // "CTRL+C"
$alt  = Keypress::detectAltKey($key);               // e.g., "ALT+X" or null
```

## Menu navigation example

```php
use Ajaxray\AnsiKit\Support\Keypress;

$i = 0; $items = ['Option 1','Option 2','Option 3'];
while (true) {
    echo "\r> " . $items[$i] . str_repeat(' ', 20);
    $key = Keypress::listen();
    if ($key === Keypress::KEY_UP)   $i = ($i - 1 + count($items)) % count($items);
    if ($key === Keypress::KEY_DOWN) $i = ($i + 1) % count($items);
    if ($key === Keypress::KEY_ENTER) break;
    if ($key === Keypress::KEY_ESC)   { $i = -1; break; }
}
```

Notes
- Terminal support varies for function/modified keys; prefer constants and provide fallbacks.
- See `tests/Support/KeypressTest.php` for exhaustive behaviors.
