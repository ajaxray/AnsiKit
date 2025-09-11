<?php

declare(strict_types=1);

namespace Ajaxray\AnsiKit\Components;

use Ajaxray\AnsiKit\AnsiTerminal;
use Ajaxray\AnsiKit\Contracts\WriterInterface;
use Ajaxray\AnsiKit\Support\Str;

/**
 * Renders a bold, emoji-prefixed banner inside a rounded box.
 *
 * Example:
 *   (emoji)  🚀
 *   (title)  "Deploy Complete"
 *   (subtitle) optional description lines
 */
final class Banner
{
    private AnsiTerminal $t;

    public function __construct(?WriterInterface $writer = null)
    {
        $this->t = new AnsiTerminal($writer);
    }

    /**
     * @param string $title Main text (bold)
     * @param string $emoji E.g. "🚀"
     * @param list<string> $lines Additional lines under title
     * @param int $padding Spaces inside box each side
     * @param array<int> $titleStyle Additional SGR codes (e.g., [AnsiTerminal::FG_GREEN])
     */
    public function render(
        string $title,
        string $emoji = '✨',
        array  $lines = [],
        int    $padding = 2,
        array  $titleStyle = [AnsiTerminal::TEXT_BOLD]
    ): void
    {
        $pad = \max(0, $padding);

        // Build visible lines
        $content = [];
        $first = \trim($emoji) !== '' ? "{$emoji}  {$title}" : $title;
        $content[] = $first;
        foreach ($lines as $l) {
            $content[] = $l;
        }

        // Compute width
        $width = 0;
        foreach ($content as $line) {
            $width = \max($width, Str::visibleLength($line));
        }
        $inner = $width + 2 * $pad;

        // Borders
        $tl = '╭'; $tr = '╮';
        $bl = '╰'; $br = '╯';
        $h = '─';
        $v = '│';

        // Top
        $this->t->write($tl . \str_repeat($h, $inner) . $tr)->newline();

        // Title line
        $this->t->write($v . \str_repeat(' ', $pad));
        $this->t->style(...$titleStyle)->write($first)->reset();
        $this->t->write(\str_repeat(' ', $inner - Str::visibleLength($first) - $pad));
        $this->t->write($v)->newline();

        // Separator (optional)
        if (!empty($lines)) {
            $this->t->write('├' . \str_repeat($h, $inner) . '┤')->newline();
        }

        // Body lines
        foreach ($lines as $line) {
            $this->t->write($v . \str_repeat(' ', $pad));
            $this->t->write($line);
            $this->t->write(\str_repeat(' ', $inner - Str::visibleLength($line) - $pad));
            $this->t->write($v)->newline();
        }

        // Bottom
        $this->t->write($bl . \str_repeat($h, $inner) . $br)->newline();
    }

    // Visible length handled by Support\Str
}
