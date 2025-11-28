---
title: Panel
parent: Components
nav_order: 7
---

# Panel Component

Create flexible container layouts for terminal applications. Panels can arrange content in vertical (rows) or horizontal (columns) layouts with optional borders and dividers.

## Basic Usage

```php
use Ajaxray\AnsiKit\Components\Panel;
use Ajaxray\AnsiKit\Components\PanelBlock;

$panel = new Panel();
$block1 = (new PanelBlock())->content('First Block (70%)')->width(40);
$block2 = (new PanelBlock())->content('Second Block (30%)')->width(17);

$panel->layout(Panel::LAYOUT_HORIZONTAL)
    ->border(true)
    ->dividers(true)
    ->addBlock($block1)
    ->addBlock($block2)
    ->render();
```

The output will be:
```terminaloutput
┌──────────────────────────────────────────────────────────────┐
│First Block (70%)                         │Second Block (30%) │
└──────────────────────────────────────────────────────────────┘
```

## Configuration Options

### Panel Configuration

| Method | Parameters | Default | Description |
|--------|------------|---------|-------------|
| `layout()` | `Panel::LAYOUT_VERTICAL` or `Panel::LAYOUT_HORIZONTAL` | `Panel::LAYOUT_VERTICAL` | Set layout direction |
| `border()` | `bool $enabled` | `false` | Enable/disable panel border |
| `dividers()` | `bool $enabled`, `string $char` | `false, '│'` | Enable dividers between blocks |
| `corners()` | `Panel::CORNER_SHARP` or `Panel::CORNER_ROUNDED` | `Panel::CORNER_SHARP` | Set corner style |
| `addBlock()` | `PanelBlock $block` | - | Add a block to the panel |

### PanelBlock Configuration

| Method | Parameters | Default | Description |
|--------|------------|---------|-------------|
| `content()` | `string $content` | `''` | Set text content |
| `width()` | `int $width` | `0` (auto) | Set fixed width |
| `height()` | `int $height` | `0` (auto) | Set fixed height |
| `border()` | `bool $enabled` | `false` | Enable/disable block border |
| `corners()` | `PanelBlock::CORNER_SHARP` or `PanelBlock::CORNER_ROUNDED` | `PanelBlock::CORNER_SHARP` | Set corner style |
| `overflow()` | `PanelBlock::OVERFLOW_EXPAND` or `PanelBlock::OVERFLOW_WORDWRAP` | `PanelBlock::OVERFLOW_EXPAND` | Handle content overflow |

## Layout Examples

### Vertical Layout (Default)

```php
$panel = new Panel();
$panel->layout(Panel::LAYOUT_VERTICAL)
    ->border(true)
    ->dividers(true)
    ->addBlock((new PanelBlock())->content('Row 1'))
    ->addBlock((new PanelBlock())->content('Row 2'))
    ->addBlock((new PanelBlock())->content('Row 3'))
    ->render();
```

Output:
```terminaloutput
┌────────────────────────────────────────┐
│Row 1                                   │
├────────────────────────────────────────┤
│Row 2                                   │
├────────────────────────────────────────┤
│Row 3                                   │
└────────────────────────────────────────┘
```

### Horizontal Layout

```php
$panel = new Panel();
$panel->layout(Panel::LAYOUT_HORIZONTAL)
    ->border(true)
    ->dividers(true)
    ->addBlock((new PanelBlock())->content('Left')->width(20))
    ->addBlock((new PanelBlock())->content('Middle')->width(20))
    ->addBlock((new PanelBlock())->content('Right')->width(20))
    ->render();
```

Output:
```terminaloutput
┌──────────────────────────────────────────────────────────────┐
│Left                │Middle              │Right               │
└──────────────────────────────────────────────────────────────┘
```

## Corner Styles

### Sharp Corners (Default)

```php
$panel = new Panel();
$panel->corners(Panel::CORNER_SHARP)
    ->border(true)
    ->addBlock((new PanelBlock())->content('Sharp corners'))
    ->render();
```

Output:
```terminaloutput
┌──────────────────────────────┐
│Sharp corners                 │
└──────────────────────────────┘
```

### Rounded Corners

```php
$panel = new Panel();
$panel->corners(Panel::CORNER_ROUNDED)
    ->border(true)
    ->addBlock((new PanelBlock())->content('Rounded corners'))
    ->render();
```

Output:
```terminaloutput
╭──────────────────────────────╮
│Rounded corners               │
╰──────────────────────────────╯
```

## Block Borders

PanelBlocks can have their own borders, independent of the panel:

```php
$panel = new Panel();
$borderedBlock1 = (new PanelBlock())
    ->content('Bordered Block 1')
    ->width(25)
    ->border(true)
    ->corners(Panel::CORNER_ROUNDED);

$borderedBlock2 = (new PanelBlock())
    ->content('Bordered Block 2')
    ->width(25)
    ->border(true)
    ->corners(Panel::CORNER_SHARP);

$panel->layout(Panel::LAYOUT_VERTICAL)
    ->addBlock($borderedBlock1)
    ->addBlock($borderedBlock2)
    ->render();
```

Output:
```terminaloutput
╭───────────────────────╮
│Bordered Block 1       │
╰───────────────────────╯
┌───────────────────────┐
│Bordered Block 2       │
└───────────────────────┘
```

## Word Wrapping

Control how content overflows the block boundaries:

```php
$longText = "This is a very long text that will be wrapped automatically when it exceeds the specified width of the block.";

$wrappedBlock = (new PanelBlock())
    ->content($longText)
    ->width(40)
    ->overflow(PanelBlock::OVERFLOW_WORDWRAP)
    ->border(true);

echo $wrappedBlock->render();
```

Output:
```terminaloutput
┌──────────────────────────────────────┐
│This is a very long text that will be │
│wrapped automatically when it exceeds │
│the specified width of the block. The │
│word wrap feature ensures content fits│
│nicely.                               │
└──────────────────────────────────────┘
```

## Custom Column Sizes

For horizontal layouts, specify custom column sizes:

```php
$panel = new Panel();
$panel->layout(Panel::LAYOUT_HORIZONTAL)
    ->border(true)
    ->dividers(true)
    ->addBlock((new PanelBlock())->content('Small')->width(10))
    ->addBlock((new PanelBlock())->content('Medium')->width(20))
    ->addBlock((new PanelBlock())->content('Large')->width(30))
    ->render();
```

Output:
```terminaloutput
┌──────────────────────────────────────────────────────────────┐
│Small     │Medium              │Large                         │
│10 cols   │20 cols             │30 cols                       │
└──────────────────────────────────────────────────────────────┘
```

## PanelBlock as Writer

PanelBlocks implement `WriterInterface`, so you can use them as writers for other components:

```php
use Ajaxray\AnsiKit\Components\Table;
use Ajaxray\AnsiKit\Components\Banner;

$panel = new Panel();
$tableBlock = new PanelBlock();
$bannerBlock = new PanelBlock();

// Render table into block
$table = new Table($tableBlock);
$table->setHeaders('Name', 'Status')
    ->addRow('Task 1', 'Done')
    ->addRow('Task 2', 'Pending')
    ->render();

// Render banner into block
$banner = new Banner($bannerBlock);
$banner->render('Success!', ['All tasks completed']);

$panel->layout(Panel::LAYOUT_VERTICAL)
    ->border(true)
    ->dividers(true)
    ->addBlock($tableBlock)
    ->addBlock($bannerBlock)
    ->render();
```

Output:
```terminaloutput
┌─────────────────────────┐
│┌────────┬─────────┐     │
││ Name   │ Status  │     │
│├────────┼─────────┤     │
││ Task 1 │ Done    │     │
││ Task 2 │ Pending │     │
│└────────┴─────────┘     │
├─────────────────────────┤
│╭───────────────────────╮│
││  Success!             ││
│├───────────────────────┤│
││  All tasks completed  ││
│╰───────────────────────╯│
└─────────────────────────┘
```

## Tips

- Panels automatically size blocks equally unless custom widths/heights are specified
- Use `overflow('wordwrap')` for long text content
- Panel and PanelBlock can have different corner styles
- PanelBlocks can be nested inside other panels for complex layouts
- Be careful about using emoji in bordered structures, as their width can vary by terminal
