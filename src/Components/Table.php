<?php

declare(strict_types=1);

namespace Ajaxray\AnsiKit\Components;

use Ajaxray\AnsiKit\AnsiTerminal;
use Ajaxray\AnsiKit\Contracts\WriterInterface;

final class Table
{
    private AnsiTerminal $t;
    private array $headers = [];
    private array $rows = [];
    private string $borderHorizontal = '─';
    private string $borderVertical = '│';
    private string $borderCross = '┼';
    private string $borderTopLeft = '┌';
    private string $borderTopRight = '┐';
    private string $borderBottomLeft = '└';
    private string $borderBottomRight = '┘';
    private string $borderHeaderCross = '┬';
    private string $borderHeaderLeft = '├';
    private string $borderHeaderRight = '┤';
    private string $borderHeaderDown = '┴';
    private int $padding = 1;
    private bool $headerBold = true;

    public function __construct(?WriterInterface $writer = null)
    {
        $this->t = new AnsiTerminal($writer);
    }

    public function setHeaders(string ...$headers): self
    {
        $this->headers = $headers;
        return $this;
    }

    /** @param list<string[]> $rows */
    public function setRows(array $rows): self
    {
        $this->rows = $rows;
        return $this;
    }

    public function addRow(string ...$cols): self
    {
        $this->rows[] = $cols;
        return $this;
    }

    public function padding(int $pad): self
    {
        $this->padding = \max(0, $pad);
        return $this;
    }

    public function headerBold(bool $bold): self
    {
        $this->headerBold = $bold;
        return $this;
    }

    /** Render table immediately to the underlying writer. */
    public function render(): void
    {
        $cols = \max(\count($this->headers), ...array_map('count', $this->rows ?: [[]]));
        $widths = \array_fill(0, $cols, 0);

        // compute widths
        for ($i = 0; $i < $cols; $i++) {
            $w = 0;
            if (isset($this->headers[$i])) {
                $w = \max($w, $this->strLen($this->headers[$i]));
            }
            foreach ($this->rows as $r) {
                if (isset($r[$i])) {
                    $w = \max($w, $this->strLen($r[$i]));
                }
            }
            $widths[$i] = $w;
        }

        $pad = \str_repeat(' ', $this->padding);

        // top border
        $this->t->write($this->borderTopLeft);
        for ($i = 0; $i < $cols; $i++) {
            $this->t->write(\str_repeat($this->borderHorizontal, $widths[$i] + 2 * $this->padding));
            $this->t->write($i === $cols - 1 ? $this->borderTopRight : $this->borderHeaderCross);
        }
        $this->t->newline();

        // header row
        if (!empty($this->headers)) {
            $this->t->write($this->borderVertical);
            for ($i = 0; $i < $cols; $i++) {
                $cell = $pad . ($this->headers[$i] ?? '') . $pad;
                $cell .= \str_repeat(' ', ($widths[$i] + 2 * $this->padding) - $this->strLen($cell));
                if ($this->headerBold) {
                    $this->t->writeStyled($cell, [AnsiTerminal::TEXT_BOLD]);
                } else {
                    $this->t->write($cell);
                }
                $this->t->write($this->borderVertical);
            }
            $this->t->newline();

            // header separator
            $this->t->write($this->borderHeaderLeft);
            for ($i = 0; $i < $cols; $i++) {
                $this->t->write(\str_repeat($this->borderHorizontal, $widths[$i] + 2 * $this->padding));
                $this->t->write($i === $cols - 1 ? $this->borderHeaderRight : $this->borderCross);
            }
            $this->t->newline();
        }

        // body rows
        foreach ($this->rows as $r) {
            $this->t->write($this->borderVertical);
            for ($i = 0; $i < $cols; $i++) {
                $txt = $r[$i] ?? '';
                $cell = $pad . $txt . $pad;
                $cell .= \str_repeat(' ', ($widths[$i] + 2 * $this->padding) - $this->strLen($cell));
                $this->t->write($cell);
                $this->t->write($this->borderVertical);
            }
            $this->t->newline();
        }

        // bottom border
        $this->t->write($this->borderBottomLeft);
        for ($i = 0; $i < $cols; $i++) {
            $this->t->write(\str_repeat($this->borderHorizontal, $widths[$i] + 2 * $this->padding));
            $this->t->write($i === $cols - 1 ? $this->borderBottomRight : $this->borderHeaderDown);
        }
        $this->t->newline();
    }

    private function strLen(string $s): int
    {
        // crude width: treat bytes as mono-width; skip ANSI SGR sequences
        $noAnsi = \preg_replace('/\e\[[0-9;]*m/', '', $s) ?? $s;
        return \mb_strlen($noAnsi);
    }
}
