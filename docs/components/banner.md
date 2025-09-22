---
title: Banner
parent: Components
nav_order: 2
---

# Banner Component

Display a title and optional lines of details inside a rounded box.

You can just print a bordered, styled text with banner component.

```php
use Ajaxray\AnsiKit\Components\Banner;

(new Banner())->render('Deploy Complete');
```
The output will be (with bold title):
```terminaloutput
╭───────────────────╮
│  Deploy Complete  │
╰───────────────────╯
```


Also, you can pass an array of description lines to render them inside the box:
```php
use Ajaxray\AnsiKit\Components\Banner;

(new Banner())
  ->render('Deploy Complete', [
    'All services are up',
    'Tag: v1.2.3',
  ]);
```
The output will be (with bold title):
```terminaloutput
╭───────────────────────╮
│  Deploy Complete      │
├───────────────────────┤
│  Everything shipped!  │
│  Tag: v1.2.3          │
╰───────────────────────╯
```

The Banner component also has options for padding and title style. For example, if you want more padding with green title:

```php
(new Banner())
  ->render('Deploy Complete', [
    'All services are up',
    'Tag: v1.2.3',
  ], 4, [AnsiTerminal::FG_GREEN]);
```

Tips:
- Keep lines short for small terminals.
- Combine with `AnsiTerminal` styles before/after if desired.
- Be careful about using emoji in bordered structures, as their width can vary by terminal.
