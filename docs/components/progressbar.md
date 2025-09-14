---
title: Progressbar
parent: Components
nav_order: 3
---

# Progressbar Component

Text progress bar with percentage, counts, and label.

```php
use Ajaxray\AnsiKit\Components\Progressbar;
use Ajaxray\AnsiKit\AnsiTerminal;

$bar = new Progressbar();
$bar->render(25, 100, 'Downloading');

// Customize
$bar->width(30)
    ->chars('▓', '▒')
    ->barStyle([AnsiTerminal::FG_GREEN])
    ->percentageStyle([AnsiTerminal::TEXT_BOLD])
    ->labelStyle([AnsiTerminal::FG_CYAN])
    ->borders('[', ']')
    ->render(50, 100, 'Halfway');
```

Notes
- `render($current, $total, ?$label)`; use `renderLine` for single-line update.
- For animations, update in a loop and flush quickly.
