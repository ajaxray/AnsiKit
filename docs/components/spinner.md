---
title: Spinner
parent: Components
nav_order: 4
---

# Spinner Component

Lightweight activity indicator.

```php
use Ajaxray\AnsiKit\Components\Spinner;

$s = new Spinner();           // default style
// $s = new Spinner(Spinner::ASCII); // alternative

for ($i = 0; $i < 20; $i++) {
    echo "\r" . $s->next() . " Working...";
    usleep(100_000);
}
echo "\r✓ Done       \n";
```

Tip
- Print with `"\r"` to update in place; end with a newline.
