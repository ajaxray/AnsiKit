<?php

declare(strict_types=1);

namespace Ajaxray\AnsiKit;

use Ajaxray\AnsiKit\Contracts\WriterInterface;
use Ajaxray\AnsiKit\Writers\StdoutWriter;
use InvalidArgumentException;

/**
 * ANSI escape sequence helper for building terminal UIs.
 *
 * - PSR-12, strict types, DI-friendly via WriterInterface.
 * - Methods are chainable.
 * - Text styles / colors as constants.
 * - 256-color and Truecolor (24-bit) helpers.
 */
final class AnsiTerminal
{
    private WriterInterface $writer;

    // Base escapes
    public const ESC = "\033";
    public const CSI = "\033[";

    // --- Text attributes (SGR) ---
    public const TEXT_RESET = 0;
    public const TEXT_BOLD = 1;
    public const TEXT_DIM = 2;
    public const TEXT_ITALIC = 3;  // not always supported
    public const TEXT_UNDERLINE = 4;
    public const TEXT_INVERSE = 7;
    public const TEXT_HIDDEN = 8;
    public const TEXT_STRIKE = 9;

    // --- Standard foreground colors (30–37), bright (90–97) ---
    public const FG_BLACK = 30;
    public const FG_RED = 31;
    public const FG_GREEN = 32;
    public const FG_YELLOW = 33;
    public const FG_BLUE = 34;
    public const FG_MAGENTA = 35;
    public const FG_CYAN = 36;
    public const FG_WHITE = 37;

    public const FG_BRIGHT_BLACK = 90;
    public const FG_BRIGHT_RED = 91;
    public const FG_BRIGHT_GREEN = 92;
    public const FG_BRIGHT_YELLOW = 93;
    public const FG_BRIGHT_BLUE = 94;
    public const FG_BRIGHT_MAGENTA = 95;
    public const FG_BRIGHT_CYAN = 96;
    public const FG_BRIGHT_WHITE = 97;

    // --- Standard background colors (40–47), bright (100–107) ---
    public const BG_BLACK = 40;
    public const BG_RED = 41;
    public const BG_GREEN = 42;
    public const BG_YELLOW = 43;
    public const BG_BLUE = 44;
    public const BG_MAGENTA = 45;
    public const BG_CYAN = 46;
    public const BG_WHITE = 47;

    public const BG_BRIGHT_BLACK = 100;
    public const BG_BRIGHT_RED = 101;
    public const BG_BRIGHT_GREEN = 102;
    public const BG_BRIGHT_YELLOW = 103;
    public const BG_BRIGHT_BLUE = 104;
    public const BG_BRIGHT_MAGENTA = 105;
    public const BG_BRIGHT_CYAN = 106;
    public const BG_BRIGHT_WHITE = 107;

    public function __construct(?WriterInterface $writer = null)
    {
        $this->writer = $writer ?? new StdoutWriter();
    }

    // ========== Low-level emitters ==========

    /** @return $this */
    private function writeRaw(string $sequence): self
    {
        $this->writer->write($sequence);
        return $this;
    }

    /** @return $this */
    public function write(string $text): self
    {
        return $this->writeRaw($text);
    }

    /** @return $this */
    public function newline(int $count = 1): self
    {
        if ($count < 1) {
            $count = 1;
        }
        return $this->writeRaw(\str_repeat(PHP_EOL, $count));
    }

    // ========== Screen & line clearing ==========

    public function clearScreen(): self
    {
        return $this->writeRaw(self::CSI . '2J');
    }

    public function clearScreenFromCursor(): self
    {
        return $this->writeRaw(self::CSI . '0J');
    }

    public function clearScreenToCursor(): self
    {
        return $this->writeRaw(self::CSI . '1J');
    }

    public function clearLine(): self
    {
        return $this->writeRaw(self::CSI . '2K');
    }

    public function clearLineFromCursor(): self
    {
        return $this->writeRaw(self::CSI . '0K');
    }

    public function clearLineToCursor(): self
    {
        return $this->writeRaw(self::CSI . '1K');
    }

    // ========== Cursor movement & visibility ==========

    public function cursorHome(): self
    {
        return $this->writeRaw(self::CSI . 'H');
    }

    public function cursorTo(int $row, int $col): self
    {
        $row = \max(1, $row);
        $col = \max(1, $col);
        return $this->writeRaw(self::CSI . $row . ';' . $col . 'H');
    }

    public function cursorUp(int $n = 1): self
    {
        return $this->writeRaw(self::CSI . \max(1, $n) . 'A');
    }

    public function cursorDown(int $n = 1): self
    {
        return $this->writeRaw(self::CSI . \max(1, $n) . 'B');
    }

    public function cursorRight(int $n = 1): self
    {
        return $this->writeRaw(self::CSI . \max(1, $n) . 'C');
    }

    public function cursorLeft(int $n = 1): self
    {
        return $this->writeRaw(self::CSI . \max(1, $n) . 'D');
    }

    public function saveCursor(): self
    {
        return $this->writeRaw(self::CSI . 's');
    }

    public function restoreCursor(): self
    {
        return $this->writeRaw(self::CSI . 'u');
    }

    public function hideCursor(): self
    {
        return $this->writeRaw(self::CSI . '?25l');
    }

    public function showCursor(): self
    {
        return $this->writeRaw(self::CSI . '?25h');
    }

    // ========== Alternate screen buffer ==========

    public function enableAltBuffer(): self
    {
        return $this->writeRaw(self::CSI . '?1049h');
    }

    public function disableAltBuffer(): self
    {
        return $this->writeRaw(self::CSI . '?1049l');
    }

    // ========== SGR (Select Graphic Rendition) ==========

    /** @param int[] $params */
    private function sgr(array $params): string
    {
        return self::CSI . \implode(';', $params) . 'm';
    }

    public function reset(): self
    {
        return $this->writeRaw($this->sgr([self::TEXT_RESET]));
    }

    /** Apply one or more SGR params (e.g., TEXT_BOLD, FG_RED). */
    public function style(int ...$params): self
    {
        if (\count($params) === 0) {
            return $this;
        }
        return $this->writeRaw($this->sgr($params));
    }

    public function fg(int $code): self
    {
        return $this->style($code);
    }

    public function bg(int $code): self
    {
        return $this->style($code);
    }

    public function fg256(int $n): self
    {
        $this->assertRange($n, 0, 255, '256-color foreground must be 0–255');
        return $this->writeRaw($this->sgr([38, 5, $n]));
    }

    public function bg256(int $n): self
    {
        $this->assertRange($n, 0, 255, '256-color background must be 0–255');
        return $this->writeRaw($this->sgr([48, 5, $n]));
    }

    /** Truecolor (24-bit) foreground: SGR 38;2;R;G;B */
    public function fgRGB(int $r, int $g, int $b): self
    {
        $this->assertRgb($r, $g, $b, 'RGB foreground');
        return $this->writeRaw($this->sgr([38, 2, $r, $g, $b]));
    }

    /** Truecolor (24-bit) background: SGR 48;2;R;G;B */
    public function bgRGB(int $r, int $g, int $b): self
    {
        $this->assertRgb($r, $g, $b, 'RGB background');
        return $this->writeRaw($this->sgr([48, 2, $r, $g, $b]));
    }

    /**
     * Write text wrapped with given styles and then reset.
     * @param string $text
     * @param int[] $styles
     */
    public function writeStyled(string $text, array $styles = []): self
    {
        if (!empty($styles)) {
            $this->writeRaw($this->sgr($styles));
        }
        $this->writeRaw($text);
        return $this->reset();
    }

    // ========== Helpers ==========

    private function assertRange(int $value, int $min, int $max, string $message): void
    {
        if ($value < $min || $value > $max) {
            throw new InvalidArgumentException($message . " (got {$value})");
        }
    }

    private function assertRgb(int $r, int $g, int $b, string $label): void
    {
        $this->assertRange($r, 0, 255, "{$label}: R must be 0–255");
        $this->assertRange($g, 0, 255, "{$label}: G must be 0–255");
        $this->assertRange($b, 0, 255, "{$label}: B must be 0–255");
    }
}