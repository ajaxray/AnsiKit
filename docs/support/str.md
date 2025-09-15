---
title: Str
parent: Support
nav_order: 2
---

# Str Helper

ANSI-aware string utilities.

```php
use Ajaxray\AnsiKit\Support\Str;

$plain = Str::stripAnsi("\033[1;31mMyText\033[0m"); // returns "MyText"
$len   = Str::visibleLength("Styled \033[1mtext\033[0m");
```

Notes
- `stripAnsi()` removes escape sequences for logs/tests.
- `visibleLength()` counts printable width for layout.

For getting visible length of a string, we are using `Grapheme::wcwidth()` from [SoloTerm/Grapheme](https://github.com/soloterm/grapheme). 
This is because `mb_strlen()`  don't work properly with some emoji and other special characters.