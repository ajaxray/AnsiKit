# AnsiKit — Build Terminal UIs in PHP

AnsiKit is a tiny, zero‑dependency helper for styling text, colors, and cursor control in the terminal — plus a few handy UI components. Perfect for small CLI tools without a heavy framework.

## Install

```bash
composer require ajaxray/ansikit
```

Requirements: PHP >= 8.2. Namespace: `Ajaxray\\AnsiKit\\` (PSR‑4).

## Quick Start

```php
<?php
require __DIR__ . '/../vendor/autoload.php';

use Ajaxray\\AnsiKit\\AnsiTerminal;
use Ajaxray\\AnsiKit\\Components\\{Table, Progressbar};

$t = new AnsiTerminal();
$t->writeStyled("Hello, AnsiKit!\n", [AnsiTerminal::TEXT_BOLD, AnsiTerminal::FG_GREEN]);
$t->fgRGB(255,165,0)->write("Truecolor (RGB) \n")->reset();

(new Table())
  ->setHeaders('Name','Age')
  ->addRow('Ada','36')
  ->addRow('Linus','54')
  ->render();

(new Progressbar())->render(42, 100, 'Downloading');
```

Run example demos:

```bash
php examples/showcase.php
php examples/progress.php
php examples/input.php
php examples/choice.php
```

## Learn More
- Core API: [AnsiTerminal](./ansi-terminal.md)
- Components: [Table](./components/table.md), [Banner](./components/banner.md), [Progressbar](./components/progressbar.md), [Spinner](./components/spinner.md), [Choice](./components/choice.md)
- Support: [Input](./support/input.md), [Str](./support/str.md), [Keypress](./support/keypress.md)

Tips: On Windows, prefer Windows Terminal or ConEmu and ensure VT processing is enabled for ANSI sequences.
# AnsiKit — Build Terminal UIs in PHP
---
title: AnsiKit
nav_order: 1
---

# AnsiKit — Build Terminal UIs in PHP
