<?php

declare(strict_types=1);

namespace Ajaxray\AnsiKit\Components;

use Ajaxray\AnsiKit\AnsiTerminal;
use Ajaxray\AnsiKit\Contracts\WriterInterface;

/**
 * Renders a customizable progress bar with percentage and optional label.
 *
 * Example:
 *   Loading files... [████████████████████████████████████████] 100% (50/50)
 *   Processing...    [██████████████████████░░░░░░░░░░░░░░░░░░░░]  75% (75/100)
 */
final class Progressbar
{
    private AnsiTerminal $t;
    private int $width = 40;
    private string $fillChar = '█';
    private string $emptyChar = '░';
    private string $leftBorder = '[';
    private string $rightBorder = ']';
    private bool $showPercentage = true;
    private bool $showCount = true;
    private array $barStyle = [];
    private array $percentageStyle = [];
    private array $labelStyle = [];

    public function __construct(?WriterInterface $writer = null)
    {
        $this->t = new AnsiTerminal($writer);
    }

    /**
     * Set the width of the progress bar (excluding borders and text).
     */
    public function width(int $width): self
    {
        $this->width = max(1, $width);
        return $this;
    }

    /**
     * Set the characters used for filled and empty portions.
     */
    public function chars(string $fillChar = '█', string $emptyChar = '░'): self
    {
        $this->fillChar = $fillChar;
        $this->emptyChar = $emptyChar;
        return $this;
    }

    /**
     * Set the border characters around the progress bar.
     */
    public function borders(string $left = '[', string $right = ']'): self
    {
        $this->leftBorder = $left;
        $this->rightBorder = $right;
        return $this;
    }

    /**
     * Configure what information to show alongside the bar.
     */
    public function display(bool $showPercentage = true, bool $showCount = true): self
    {
        $this->showPercentage = $showPercentage;
        $this->showCount = $showCount;
        return $this;
    }

    /**
     * Set styling for the progress bar itself.
     * @param array<int> $styles SGR codes (e.g., [AnsiTerminal::FG_GREEN])
     */
    public function barStyle(array $styles): self
    {
        $this->barStyle = $styles;
        return $this;
    }

    /**
     * Set styling for the percentage text.
     * @param array<int> $styles SGR codes (e.g., [AnsiTerminal::TEXT_BOLD])
     */
    public function percentageStyle(array $styles): self
    {
        $this->percentageStyle = $styles;
        return $this;
    }

    /**
     * Set styling for the label text.
     * @param array<int> $styles SGR codes (e.g., [AnsiTerminal::FG_CYAN])
     */
    public function labelStyle(array $styles): self
    {
        $this->labelStyle = $styles;
        return $this;
    }

    /**
     * Render the progress bar.
     *
     * @param int $current Current progress value
     * @param int $total Total/maximum value
     * @param string $label Optional label to show before the bar
     */
    public function render(int $current, int $total, string $label = ''): void
    {
        $current = max(0, min($current, $total));
        $total = max(1, $total);
        
        $percentage = $total > 0 ? ($current / $total) * 100 : 0;
        $fillWidth = (int) round(($current / $total) * $this->width);
        $emptyWidth = $this->width - $fillWidth;

        // Render label if provided
        if ($label !== '') {
            $this->t->writeStyled($label, $this->labelStyle);
            $this->t->write(' ');
        }

        // Render progress bar
        $this->t->write($this->leftBorder);

        // Progress bar content (fill + empty)
        $barContent = str_repeat($this->fillChar, $fillWidth) . str_repeat($this->emptyChar, $emptyWidth);
        $this->t->writeStyled($barContent, $this->barStyle);

        $this->t->write($this->rightBorder);

        // Render percentage
        if ($this->showPercentage) {
            $this->t->write(' ');
            $this->t->writeStyled(sprintf('%3.0f%%', $percentage), $this->percentageStyle);
        }

        // Render count
        if ($this->showCount) {
            $this->t->write(sprintf(' (%d/%d)', $current, $total));
        }
    }

    /**
     * Render progress bar and move to next line.
     */
    public function renderLine(int $current, int $total, string $label = ''): void
    {
        $this->render($current, $total, $label);
        $this->t->newline();
    }

    /**
     * Render progress bar, then move cursor back to beginning of line.
     * Useful for updating progress in place.
     */
    public function renderInPlace(int $current, int $total, string $label = ''): void
    {
        $this->t->write("\r"); // Carriage return to beginning of line
        $this->render($current, $total, $label);
        $this->t->write("\r"); // Return to beginning for next update
    }

}
