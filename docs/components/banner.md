---
title: Banner
parent: Components
nav_order: 2
---

# Banner Component

Display a title with an optional emoji/icon and lines of details.

```php
use Ajaxray\AnsiKit\Components\Banner;

(new Banner())
  ->render('Deploy Complete', '🚀', [
    'All services are up',
    'Tag: v1.2.3',
  ]);
```

Tips
- Keep lines short for small terminals.
- Combine with `AnsiTerminal` styles before/after if desired.
