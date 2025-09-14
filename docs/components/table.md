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

Notes
- Auto-sizes columns based on content.
- Uses box-drawing characters; monospace fonts recommended.
