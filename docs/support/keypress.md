---
title: Keypress
parent: Support
nav_order: 3
---

# Keypress — Interactive Keyboard Input

Read raw keyboard input and work with normalized key names. 
Supports arrows, ENTER/ESC/TAB/BACKSPACE, CTRL+A..Z, F1..F12, HOME/END/PgUp/PgDn, and modified arrows (Ctrl/Alt/Shift).

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

## What you get

- Arrows and basics: `KEY_UP`, `KEY_DOWN`, `KEY_LEFT`, `KEY_RIGHT`, `KEY_ENTER`, `KEY_SPACE`, `KEY_BACKSPACE`, `KEY_TAB`, `KEY_ESC`
- Ctrl combos: `KEY_CTRL_A` .. `KEY_CTRL_Z`
- Function keys: `KEY_F1` .. `KEY_F12`
- Navigation: `KEY_HOME`, `KEY_END`, `KEY_PAGE_UP`, `KEY_PAGE_DOWN`, `KEY_INSERT`, `KEY_DELETE`
- Modified arrows: `KEY_CTRL_UP/DOWN/LEFT/RIGHT`, `KEY_ALT_*`, `KEY_SHIFT_*`

Unknown sequences are returned as‑is (raw string), and printable single characters are returned directly (e.g., `'a'`).

```php
// Example: switch by normalized keys
switch ($key = Keypress::listen()) {
    case Keypress::KEY_UP:   /* ... */ break;
    case Keypress::KEY_DOWN: /* ... */ break;
    case Keypress::KEY_ENTER: /* select */ break;
    case 'q': /* quit */ break;           // regular characters come through as-is
}
```

## Non-blocking reads

Use `listenNonBlocking($timeoutMs)` to poll without freezing your UI loop:

```php
use Ajaxray\AnsiKit\Support\Keypress;

while (true) {
    // render/update UI...
    if ($key = Keypress::listenNonBlocking(50)) {
        if ($key === Keypress::KEY_ESC) break;
        // handle key...
    }
    // do other work, animation ticks, etc.
}
```

## Helpers

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

`getKeyName()` is handy for debugging or displaying feedback; `detectAltKey()` recognizes simple Alt+<key> sequences that come as ESC-prefixed printable chars.

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

## Show pressed key info (debug)

```php
use Ajaxray\AnsiKit\Support\Keypress;

echo "Press keys (ESC to exit)\n";
while (true) {
    $raw = Keypress::listen();
    if ($raw === Keypress::KEY_ESC) break;
    $name = Keypress::getKeyName($raw);
    printf("Key: %-14s Raw: %s\n", $name, json_encode($raw));
}
```

Tips
- Terminal support can vary for function/modified keys; prefer constants and provide fallbacks for raw sequences.
- See `tests/Support/KeypressTest.php` and `examples/keypress*.php` for comprehensive behaviors.
