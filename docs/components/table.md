---
title: Table
parent: Components
nav_order: 1
---

# Table Component

Render simple data tables with borders.

```php
use Ajaxray\AnsiKit\Components\Table;

(new Table())
  ->setHeaders('Name','Age','City')
  ->addRow('Ada','36','London')
  ->addRow('Linus','54','Helsinki')
  ->render();
```
The output will be (with bold headers):
```terminaloutput
┌───────┬─────┬──────────┐
│ Name  │ Age │ City     │
├───────┼─────┼──────────┤
│ Ada   │ 36  │ London   │
│ Linus │ 54  │ Helsinki │
└───────┴─────┴──────────┘
```

Notes
- Auto-sizes columns based on content.
- Uses box-drawing characters; monospace fonts recommended.
- Be careful about using emoji in bordered structures, as their width can vary by terminal.
