---
title: Util
parent: Support
nav_order: 4
---

# Util Helper

Small convenience methods for generic terminal control.

## Beep (BEL)

Emit the ASCII BEL character (`\007`) either via an `AnsiTerminal` instance or straight to STDOUT.

```php
use Ajaxray\AnsiKit\Support\Util;
use Ajaxray\AnsiKit\AnsiTerminal;
use Ajaxray\AnsiKit\Writers\MemoryWriter;

$terminal = new AnsiTerminal(new MemoryWriter());
Util::beep($terminal);   // writes BEL through the terminal writer

Util::beep();            // falls back to php://output (STDOUT)
```

The helper is handy in tests and scripted demos where you want the terminal bell but do not want to manage raw control codes yourself.

## Set Terminal / Tab Title

Update the terminal window or tab title using OSC 0 (`ESC]0;...BEL`).

```php
use Ajaxray\AnsiKit\Support\Util;
use Ajaxray\AnsiKit\AnsiTerminal;

$terminal = new AnsiTerminal();
Util::setTerminalTabTitle('AnsiKit Demo', $terminal);
```

When no `AnsiTerminal` is supplied the sequence is written to STDOUT, so it also works in quick scripts:

```php
Util::setTerminalTabTitle('Deploy in progress');
```

Tip: remember to restore the previous title before exiting long-running tasks. The `examples/util.php` script shows one way to do this interactively.
