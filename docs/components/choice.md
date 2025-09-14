---
title: Choice
parent: Components
nav_order: 5
---

# Choice Component

Interactive single-choice prompt with optional Exit.

```php
use Ajaxray\AnsiKit\Components\Choice;
use Ajaxray\AnsiKit\AnsiTerminal;

$choice = new Choice();
$selected = $choice
  ->prompt('Choose an action:', ['Deploy', 'Test', 'Rollback']);

echo "Selected: $selected\n";

// Optional choice (with Exit)
$selected = (new Choice())
  ->required(false)
  ->prompt('Choose:', ['A','B','C']);

// Styling
(new Choice())
  ->promptStyle([AnsiTerminal::TEXT_BOLD, AnsiTerminal::FG_CYAN])
  ->optionStyle([AnsiTerminal::FG_GREEN])
  ->numberStyle([AnsiTerminal::FG_YELLOW])
  ->errorStyle([AnsiTerminal::FG_RED])
  ->exitStyle([AnsiTerminal::FG_BRIGHT_BLACK])
  ->prompt('Select:', ['One','Two']);
```

Notes
- Returns selected label (or `false` when optional Exit chosen).
- Validates numeric input; trims whitespace.
