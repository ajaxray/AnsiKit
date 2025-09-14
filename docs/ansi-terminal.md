---
title: AnsiTerminal
nav_order: 2
---

# AnsiTerminal — Core API

`Ajaxray\AnsiKit\AnsiTerminal` provides chainable styling, color, and cursor control.

## Basics

```php
use Ajaxray\AnsiKit\AnsiTerminal;
$t = new AnsiTerminal();
$t->write("Plain text\n");
$t->writeStyled("Bold Red\n", [AnsiTerminal::TEXT_BOLD, AnsiTerminal::FG_RED]);
```

## Colors

```php
// 8/16 colors
$t->style(AnsiTerminal::FG_GREEN, AnsiTerminal::BG_BLACK)->write("OK")->reset();
// 256-color
$t->fg256(202)->bg256(235)->write("256-colors")->reset();
// Truecolor (RGB)
$t->fgRGB(255,165,0)->write("Orange")->reset();
```

## Cursor & Screen

```php
$t->clearScreen()->cursorHome();
$t->cursorTo(10, 5)->write("at row 10 col 5");
$t->cursorUp(2)->write("moved up");
$t->hideCursor();
// ... do work ...
$t->showCursor();
```

## Newlines and Output

```php
$t->newline();        // one newline
$t->newline(2);       // two newlines
$t->write("Hello");  // no newline
$t->writeStyled("Bold Red\n", [AnsiTerminal::TEXT_BOLD, AnsiTerminal::FG_RED]);
```

Tip: In tests, pass `Writers\MemoryWriter` to capture output.
