---
title: PanelBlock
parent: Components
nav_order: 8
---

# PanelBlock Component

Individual content blocks that can be used standalone or within Panels. PanelBlocks implement `WriterInterface`, making them versatile containers for text or other components.

## Basic Usage

```php
use Ajaxray\AnsiKit\Components\PanelBlock;

$block = new PanelBlock();
$block->content('Hello, World!')
    ->width(30)
    ->border(true)
    ->render();
```

The output will be:
```terminaloutput
┌──────────────────────────────┐
│Hello, World!                 │
└──────────────────────────────┘
```

## Configuration Options

| Method | Parameters | Default | Description |
|--------|------------|---------|-------------|
| `content()` | `string $content` | `''` | Set text content |
| `width()` | `int $width` | `0` (auto) | Set fixed width |
| `height()` | `int $height` | `0` (auto) | Set fixed height |
| `border()` | `bool $enabled` | `false` | Enable/disable block border |
| `corners()` | `PanelBlock::CORNER_SHARP` or `PanelBlock::CORNER_ROUNDED` | `PanelBlock::CORNER_SHARP` | Set corner style |
| `overflow()` | `PanelBlock::OVERFLOW_EXPAND` or `PanelBlock::OVERFLOW_WORDWRAP` | `PanelBlock::OVERFLOW_EXPAND` | Handle content overflow |

## Corner Styles

### Sharp Corners (Default)

```php
$block = (new PanelBlock())
    ->content('Sharp corners')
    ->width(25)
    ->border(true)
    ->corners(PanelBlock::CORNER_SHARP);

echo $block->render();
```

Output:
```terminaloutput
┌─────────────────────┐
│Sharp corners        │
└─────────────────────┘
```

### Rounded Corners

```php
$block = (new PanelBlock())
    ->content('Rounded corners')
    ->width(25)
    ->border(true)
    ->corners(PanelBlock::CORNER_ROUNDED);

echo $block->render();
```

Output:
```terminaloutput
╭─────────────────────╮
│Rounded corners      │
╰─────────────────────╯
```

## Content Overflow

### Expand Mode (Default)

Content expands beyond the specified width:

```php
$block = (new PanelBlock())
    ->content('This is a very long line that will extend beyond the width')
    ->width(20)
    ->overflow(PanelBlock::OVERFLOW_EXPAND)
    ->border(true);

echo $block->render();
```

Output:
```terminaloutput
┌──────────────────────────────────────────────────────────────┐
│This is a very long line that will extend beyond the width    │
└──────────────────────────────────────────────────────────────┘
```

### Word Wrap Mode

Content wraps to fit within the specified width:

```php
$block = (new PanelBlock())
    ->content('This is a very long line that will be wrapped to fit within the width')
    ->width(30)
    ->overflow(PanelBlock::OVERFLOW_WORDWRAP)
    ->border(true);

echo $block->render();
```

Output:
```terminaloutput
┌──────────────────────────────┐
│This is a very long line that │
│will be wrapped to fit within │
│the width                     │
└──────────────────────────────┘
```

## Multi-line Content

PanelBlocks handle multi-line content automatically:

```php
$block = (new PanelBlock())
    ->content("Line 1\nLine 2\nLine 3")
    ->width(20)
    ->border(true);

echo $block->render();
```

Output:
```terminaloutput
┌────────────────────┐
│Line 1              │
│Line 2              │
│Line 3              │
└────────────────────┘
```

## Fixed Height

Control the exact height of the block:

```php
$block = (new PanelBlock())
    ->content('Short content')
    ->width(25)
    ->height(5)
    ->border(true);

echo $block->render();
```

Output:
```terminaloutput
┌─────────────────────┐
│Short content        │
│                     │
│                     │
│                     │
│                     │
└─────────────────────┘
```

## As Writer for Other Components

PanelBlocks implement `WriterInterface`, making them perfect containers for other components:

```php
use Ajaxray\AnsiKit\Components\Table;
use Ajaxray\AnsiKit\Components\Banner;

// Table in a block
$tableBlock = new PanelBlock();
$table = new Table($tableBlock);
$table->setHeaders('Name', 'Status')
    ->addRow('Task 1', 'Done')
    ->addRow('Task 2', 'Pending')
    ->render();

echo $tableBlock->render();
```

Output:
```terminaloutput
┌────────┬─────────┐
│ Name   │ Status  │
├────────┼─────────┤
│ Task 1 │ Done    │
│ Task 2 │ Pending │
└────────┴─────────┘
```

```php
// Banner in a block
$bannerBlock = new PanelBlock();
$banner = new Banner($bannerBlock);
$banner->render('Success!', ['All tasks completed']);

echo $bannerBlock->render();
```

Output:
```terminaloutput
╭───────────────────────╮
│  Success!             │
├───────────────────────┤
│  All tasks completed  │
╰───────────────────────╯
```

## Styling with ANSI Terminal

Combine with `AnsiTerminal` for styled content:

```php
use Ajaxray\AnsiKit\AnsiTerminal;
use Ajaxray\AnsiKit\Components\PanelBlock;

$t = new AnsiTerminal();
$styledContent = $t->colorize('Styled Text', [AnsiTerminal::FG_GREEN, AnsiTerminal::TEXT_BOLD]);

$block = (new PanelBlock())
    ->content($styledContent)
    ->width(25)
    ->border(true)
    ->corners(PanelBlock::CORNER_ROUNDED);

echo $block->render();
```

Output (with green bold text):
```terminaloutput
╭─────────────────────╮
│Styled Text          │
╰─────────────────────╯
```

## Tips

- Use PanelBlocks as standalone bordered containers or within Panels
- Set `overflow('wordwrap')` for long text content
- PanelBlocks automatically trim trailing newlines to prevent empty lines
- Combine with `AnsiTerminal` styles for rich text formatting
- Perfect for creating custom layouts by nesting components
- Be careful about using emoji in bordered structures, as their width can vary by terminal
