<p align="center">
  <img src="media/AnsiKit.png" alt="AnsiKit" />
</p>

# AnsiKit 

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ajaxray/ansikit.svg?style=flat-square)](https://packagist.org/packages/ajaxray/ansikit)
[![Total Downloads](https://img.shields.io/packagist/dt/ajaxray/ansikit.svg?style=flat-square)](https://packagist.org/packages/ajaxray/ansikit)
[![Build Status](https://github.com/ajaxray/ansikit/actions/workflows/php.yml/badge.svg)](https://github.com/ajaxray/ansikit/actions)
[![License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE)


Tiny, easy-to-remember ANSI escape helper for building terminal UIs in PHP. Chainable API for styles/colors/cursor control, with a few handy components (Table, Banner, Progressbar etc.) and utilities.

Works any terminal that supports ANSI escapes (most modern terminals on macOS/Linux and Windows Terminal/ConEmu on Windows 10+).

> **Let's keep simple things simple.** 😌  
> You don't need a _whole framework_ to build a simple CLI app.

## Features

- Colors: Foreground and background colors - standard 8/16 colors, 256-color, and truecolor (RGB, 24-bit)
- Text styles: bold, dim, italic, underline, inverse, hidden, strike-through
- Cursor control: movement, save/restore, show/hide, alternate screen buffer
- Components: Table, Banner, Progressbar, Spinner
- Writers: swap output target (stdout or in-memory for tests)
- Helpers: ANSI‑aware string length, simple input (line/multiline/confirm)
- PSR‑12, strict types, DI‑friendly, zero dependencies

## A Quick Primer

Run the example demo: `php examples/showcase.php`.  
This should render the following in dark mode terminal:
![showcase.png](media/showcase.png)

Check below the [Examples](#examples) section for more demos.

## Installation

Requirements: PHP >= 8.2 and (composer)[https://getcomposer.org/]

```bash
composer require ajaxray/ansikit
```

Autoloads via PSR‑4 namespace `Ajaxray\AnsiKit\`.

## Documentation

- Getting started: [docs/index.md](docs/index.md)
- Core API: [AnsiTerminal](docs/ansi-terminal.md)
- Components: [Table](docs/components/table.md), [Banner](docs/components/banner.md), [Progressbar](docs/components/progressbar.md), [Spinner](docs/components/spinner.md), [Choice](docs/components/choice.md)
- Support: [Input](docs/support/input.md), [Str](docs/support/str.md), [Keypress](docs/support/keypress.md), [Util](docs/support/util.md)

## Quick Start

```php
<?php
require __DIR__ . '/vendor/autoload.php';

use Ajaxray\AnsiKit\AnsiTerminal;
use Ajaxray\AnsiKit\Components\{Table, Banner, Progressbar, Choice};

$t = new AnsiTerminal();
$t->clearScreen()->cursorHome();

$t->writeStyled("Hello PHP Ninja!\n", [AnsiTerminal::TEXT_BOLD, AnsiTerminal::FG_GREEN]);
$t->fg256(202)->bg256(235)->write("256-colors")->reset()->newline();
$t->fgRGB(255,165,0)->write("Truecolor (RGB)")->reset()->newline();

$table = new Table();
$table->setHeaders('Name','Age', 'Adult?')
    ->addRow('Anis','41', '✅')
    ->addRow('Fatima', '2.5', '❌')
    ->render();

$banner = new Banner();
$banner->render('Deploy Complete', ['Everything shipped!']);

$bar = new Progressbar();
$bar->renderLine(75, 100, 'Loading files...');

$choice = new Choice();
$selected = $choice->prompt('Choose deployment target:', ['Production', 'Staging', 'Development']);
$t->writeStyled("Selected: {$selected}\n", [AnsiTerminal::FG_GREEN]);
```

## Usage Overview

### Core: `AnsiTerminal`

Docs: [AnsiTerminal](docs/ansi-terminal.md)

- Text attributes: `TEXT_BOLD`, `TEXT_UNDERLINE`, `TEXT_STRIKE`, etc.
- Foreground/background colors: standard (`FG_RED`), bright (`FG_BRIGHT_GREEN`), `fg256($n)`, `bg256($n)`, `fgRGB($r,$g,$b)`, `bgRGB(...)`
- Cursor: `cursorTo($row, $col)`, `cursorUp($n)`, `hideCursor()`, `saveCursor()`, `enableAltBuffer()`
- Output: `write($text)`, `writeStyled($text, [..])`, `newline($n)`

Chainable style example:

```php
$t->style(AnsiTerminal::TEXT_BOLD, AnsiTerminal::FG_YELLOW)->write('Warning!')->reset()->newline();
```

### Components

#### Table

Docs: [Table](docs/components/table.md)

```php
use Ajaxray\AnsiKit\Components\Table;

(new Table())
  ->setHeaders('Name','Age')
  ->addRow('Ada', '36')
  ->addRow('Linus', '54')
  ->render();
```

#### Banner

Docs: [Banner](docs/components/banner.md)

```php
use Ajaxray\AnsiKit\Components\Banner;

(new Banner())->render('Deploy Complete', ['Everything shipped!', 'Tag: v1.2.3']);
```

#### Progressbar

Docs: [Progressbar](docs/components/progressbar.md)

```php
use Ajaxray\AnsiKit\Components\Progressbar;

(new Progressbar())
  ->barStyle([AnsiTerminal::FG_GREEN])
  ->percentageStyle([AnsiTerminal::TEXT_BOLD])
  ->labelStyle([AnsiTerminal::FG_CYAN])
  ->borders('[', ']')
  ->renderLine(50, 100, 'Processing');
```

#### Spinner

Docs: [Spinner](docs/components/spinner.md)

```php
use Ajaxray\AnsiKit\Components\Spinner;

$s = new Spinner(); // or new Spinner(Spinner::ASCII)
echo $s->next(); // prints next frame
```

#### Choice

Docs: [Choice](docs/components/choice.md)

```php
use Ajaxray\AnsiKit\Components\Choice;

// Basic usage (required choice)
$choice = new Choice();
$selected = $choice->prompt('Choose an option:', ['Option A', 'Option B', 'Option C']);
// Returns: 'Option A', 'Option B', or 'Option C'

// Optional choice (with Exit option)
$choice = new Choice();
$selected = $choice
  ->required(false)
  ->prompt('Select an action:', ['Deploy', 'Test', 'Rollback']);
// Returns: 'Deploy', 'Test', 'Rollback', or false (if Exit chosen)

// Styled choice
$choice = new Choice();
$selected = $choice
  ->promptStyle([AnsiTerminal::TEXT_BOLD, AnsiTerminal::FG_CYAN])
  ->optionStyle([AnsiTerminal::FG_GREEN])
  ->numberStyle([AnsiTerminal::FG_YELLOW])
  ->errorStyle([AnsiTerminal::FG_RED])
  ->exitStyle([AnsiTerminal::FG_BRIGHT_BLACK])
  ->prompt('Choose:', ['Option 1', 'Option 2']);
```

### Helpers

#### Input

Docs: [Input](docs/support/input.md)

```php
use Ajaxray\AnsiKit\Support\Input;

$name = Input::line('Your name? [Anonymous] ', 'Anonymous');
$ok   = Input::confirm('Proceed?', true);
$bio  = Input::multiline("Enter bio. End with 'END'", 'END');
```

If the `readline` extension is available, `Input::line()` uses it for line editing/history; otherwise it falls back to `STDIN`.

#### Str

Docs: [Str](docs/support/str.md)

```php
use Ajaxray\AnsiKit\Support\Str;

$plain = Str::stripAnsi("\033[1;31mError\033[0m");
$len   = Str::visibleLength("Styled \033[1mtext\033[0m");
```

#### Util

Docs: [Util](docs/support/util.md)

```php
use Ajaxray\AnsiKit\Support\Util;
use Ajaxray\AnsiKit\AnsiTerminal;

$terminal = new AnsiTerminal();
Util::setTerminalTabTitle('Build in progress', $terminal);
Util::beep($terminal);
```

#### Keypress

Docs: [Keypress](docs/support/keypress.md)

```php
use Ajaxray\AnsiKit\Support\Keypress;

// Blocking read (returns a normalized key constant or single char)
$key = Keypress::listen();
if ($key === Keypress::KEY_UP) { /* move up */ }

// Non-blocking read with timeout (in milliseconds)
if ($key = Keypress::listenNonBlocking(100)) {
    // Works with arrows, ENTER/ESC/TAB/BACKSPACE, CTRL+A..Z, F1..F12, HOME/END/PgUp/PgDn, and more
    echo Keypress::getKeyName($key); // e.g., "CTRL+C", "UP ARROW", "'a'"
}
```

### Writers

- `StdoutWriter` (default): writes to `php://stdout` or a provided stream
- `MemoryWriter`: buffer output for tests or capturing

```php
use Ajaxray\AnsiKit\Writers\MemoryWriter;

$w = new MemoryWriter();
$t = new AnsiTerminal($w);
$t->write('hello');
echo $w->getBuffer(); // 'hello'
```

## Examples

Run the example scripts to see things in action:

```bash
php examples/showcase.php   # basic styles, table, banner, bars
php examples/progress.php   # animated status + progress bar
php examples/input.php      # interactive input demo
php examples/choice.php     # interactive choice component demo
php examples/choice-menu.php # interactive menu system with choice
php examples/keypress.php    # interactive key handling demo
php examples/keypress-advanced.php # advanced key sequences, modifiers
php examples/util.php        # tab title + bell helper demo
```

More guides and examples: see [docs/index.md](docs/index.md).

## Tips & Compatibility

- Windows 10+: use Windows Terminal, ConEmu, or enable VT processing for best ANSI support
- Use monospaced fonts for best alignment of box-drawing characters
- Emoji width can vary by terminal; table/banner widths are based on naive visible length

## Additional Resources

This library help with rendering basic terminal UIs with styles, colors, and cursor control. 
For more complex TUIs and interactive apps, you may use:

- Parsing options and flags: 
  - For simpler apps, [$argv](https://www.php.net/manual/en/reserved.variables.argv.php) and [getopt()](https://www.marsceau.com/blog/php-getopt/) should be sufficient
  - For more complex apps, [ulrichsg/getopt-php](https://getopt-php.github.io/getopt-php/) can be used
- Interactive menu: [php-school/cli-menu](https://github.com/php-school/cli-menu) 
- Desktop Notification: [JoliNotif](https://github.com/jolicode/JoliNotif)

### What if I need a full-featured CLI framework?
In case you are planning for a multi-command, complex app that requires guided structure and organization, you may check: 
- [Symfony Console Component](https://symfony.com/doc/current/components/console.html)
- [Laravel Zero](https://laravel-zero.com/)
- [MiniCLI](https://docs.minicli.dev/en/latest/)

## Contributing

Contributions are welcome! Please:

1. Fork and clone the repo
2. Create a feature branch: `git checkout -b feat/your-thing`
3. Install dev deps: `composer install`
4. Run tests: `composer test`
5. Follow PSR‑12 and keep changes focused
6. Open a PR with a clear description and before/after context

If you change public APIs, update examples and this README. Small, focused PRs are easier to review.

## Roadmap (Ideas)

- ANSI-aware padding/truncation and text alignment helpers
- Color themes/palettes and named styles
- Table cell alignment and per-column styling
- Minimal TUI widgets (prompt list, select, progress spinner lines)

## Versioning & License

- Versioned with SemVer (0.x may include minor BC tweaks)
- Licensed under the MIT License — see `LICENSE`

---

Made with ❤️ for terminal builders. If you ship something with AnsiKit, I’d love to hear about it!
