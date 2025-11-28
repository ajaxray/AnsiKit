<?php

declare(strict_types=1);

namespace Ajaxray\AnsiKit\Components;

use Ajaxray\AnsiKit\Contracts\WriterInterface;
use Ajaxray\AnsiKit\Support\Str;

/**
 * Individual block within a Panel that implements WriterInterface.
 * Can contain text content or be used as a writer for other components.
 */
final class PanelBlock implements WriterInterface
{
    private string $content = '';
    private bool $hasBorder = false;
    private string $overflow = 'expand'; // 'expand' or 'wordwrap'
    private int $fixedWidth = 0;
    private int $fixedHeight = 0;
    private ?WriterInterface $writer = null;
    private string $cornerStyle = 'sharp'; // 'sharp' or 'rounded'
    
    // Corner characters for sharp style
    private string $sharpTopLeft = '┌';
    private string $sharpTopRight = '┐';
    private string $sharpBottomLeft = '└';
    private string $sharpBottomRight = '┘';
    
    // Corner characters for rounded style
    private string $roundedTopLeft = '╭';
    private string $roundedTopRight = '╮';
    private string $roundedBottomLeft = '╰';
    private string $roundedBottomRight = '╯';

    public function __construct(?WriterInterface $writer = null)
    {
        $this->writer = $writer;
    }

    /**
     * Set text content for the block.
     */
    public function content(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Get current content.
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Enable/disable border for this block.
     */
    public function border(bool $enabled = true): self
    {
        $this->hasBorder = $enabled;
        return $this;
    }

    /**
     * Set overflow behavior: 'expand' or 'wordwrap'.
     */
    public function overflow(string $mode): self
    {
        if (!in_array($mode, ['expand', 'wordwrap'], true)) {
            throw new \InvalidArgumentException('Overflow mode must be "expand" or "wordwrap"');
        }
        $this->overflow = $mode;
        return $this;
    }

    /**
     * Set fixed width for the block.
     */
    public function width(int $width): self
    {
        $this->fixedWidth = max(0, $width);
        return $this;
    }

    /**
     * Set fixed height for the block.
     */
    public function height(int $height): self
    {
        $this->fixedHeight = max(0, $height);
        return $this;
    }

    /**
     * Set corner style: 'sharp' or 'rounded'.
     */
    public function corners(string $style): self
    {
        if (!in_array($style, ['sharp', 'rounded'], true)) {
            throw new \InvalidArgumentException('Corner style must be "sharp" or "rounded"');
        }
        $this->cornerStyle = $style;
        return $this;
    }

    /**
     * Get the current corner characters based on style.
     */
    private function getCornerCharacters(): array
    {
        if ($this->cornerStyle === 'rounded') {
            return [
                'topLeft' => $this->roundedTopLeft,
                'topRight' => $this->roundedTopRight,
                'bottomLeft' => $this->roundedBottomLeft,
                'bottomRight' => $this->roundedBottomRight,
            ];
        }
        
        return [
            'topLeft' => $this->sharpTopLeft,
            'topRight' => $this->sharpTopRight,
            'bottomLeft' => $this->sharpBottomLeft,
            'bottomRight' => $this->sharpBottomRight,
        ];
    }

    /**
     * Get the content width (excluding borders).
     */
    public function getContentWidth(): int
    {
        if ($this->fixedWidth > 0) {
            return $this->fixedWidth - ($this->hasBorder ? 2 : 0);
        }
        
        $lines = $this->getContentLines();
        $maxWidth = 0;
        foreach ($lines as $line) {
            $maxWidth = max($maxWidth, Str::visibleLength($line));
        }
        return $maxWidth;
    }

    /**
     * Get the content height (excluding borders).
     */
    public function getContentHeight(): int
    {
        if ($this->fixedHeight > 0) {
            return $this->fixedHeight - ($this->hasBorder ? 2 : 0);
        }
        
        return count($this->getContentLines());
    }

    /**
     * Get the total width including borders.
     */
    public function getTotalWidth(): int
    {
        return $this->getContentWidth() + ($this->hasBorder ? 2 : 0);
    }

    /**
     * Get the total height including borders.
     */
    public function getTotalHeight(): int
    {
        return $this->getContentHeight() + ($this->hasBorder ? 2 : 0);
    }

    /**
     * WriterInterface implementation - writes to internal buffer or provided writer.
     */
    public function write(string $bytes): int
    {
        if ($this->writer !== null) {
            return $this->writer->write($bytes);
        }
        
        $this->content .= $bytes;
        return strlen($bytes);
    }

    /**
     * Get processed content lines with word wrapping if needed.
     */
    private function getContentLines(): array
    {
        if (empty($this->content)) {
            return [''];
        }

        if ($this->overflow === 'wordwrap' && $this->fixedWidth > 0) {
            $contentWidth = $this->fixedWidth - ($this->hasBorder ? 2 : 0);
            return $this->wordWrap($this->content, $contentWidth);
        }
        
        // Trim trailing newlines to avoid empty lines at the end
        $content = rtrim($this->content, "\n");
        if (empty($content)) {
            return [''];
        }
        
        return explode("\n", $content);
    }

    /**
     * Simple word wrap implementation.
     */
    private function wordWrap(string $text, int $width): array
    {
        if ($width <= 0) {
            return [$text];
        }
        
        $inputLines = explode("\n", $text);
        $wrappedLines = [];
        
        foreach ($inputLines as $inputLine) {
            $words = explode(' ', $inputLine);
            $currentLine = '';
            
            foreach ($words as $word) {
                $testLine = $currentLine === '' ? $word : $currentLine . ' ' . $word;
                
                if (Str::visibleLength($testLine) <= $width) {
                    $currentLine = $testLine;
                } else {
                    if ($currentLine !== '') {
                        $wrappedLines[] = $currentLine;
                        $currentLine = $word;
                    } else {
                        // Word is longer than width, break it
                        while (Str::visibleLength($word) > $width) {
                            $wrappedLines[] = substr($word, 0, $width);
                            $word = substr($word, $width);
                        }
                        $currentLine = $word;
                    }
                }
            }
            
            if ($currentLine !== '') {
                $wrappedLines[] = $currentLine;
            }
        }
        
        return empty($wrappedLines) ? [''] : $wrappedLines;
    }

    /**
     * Render the block content as lines (with border if enabled).
     * @return list<string>
     */
    public function renderLines(): array
    {
        $lines = $this->getContentLines();
        $contentWidth = $this->fixedWidth > 0 ? $this->fixedWidth - ($this->hasBorder ? 2 : 0) : $this->getContentWidth();
        $contentHeight = $this->fixedHeight > 0 ? $this->fixedHeight - ($this->hasBorder ? 2 : 0) : count($lines);
        
        // Pad lines to content height
        while (count($lines) < $contentHeight) {
            $lines[] = '';
        }
        
        // Truncate if too many lines
        if (count($lines) > $contentHeight) {
            $lines = array_slice($lines, 0, $contentHeight);
        }
        
        // Pad each line to content width
        foreach ($lines as &$line) {
            $visibleLen = Str::visibleLength($line);
            if ($visibleLen < $contentWidth) {
                $line .= str_repeat(' ', $contentWidth - $visibleLen);
            } elseif ($visibleLen > $contentWidth) {
                // Truncate if too long
                $line = substr($line, 0, $contentWidth);
            }
        }
        
        // Add border if enabled
        if ($this->hasBorder) {
            $corners = $this->getCornerCharacters();
            $borderedLines = [];
            $borderedLines[] = $corners['topLeft'] . str_repeat('─', $contentWidth) . $corners['topRight'];
            foreach ($lines as $line) {
                $borderedLines[] = '│' . $line . '│';
            }
            $borderedLines[] = $corners['bottomLeft'] . str_repeat('─', $contentWidth) . $corners['bottomRight'];
            return $borderedLines;
        }
        
        return $lines;
    }

    /**
     * Render the block with optional border as a string.
     */
    public function render(): string
    {
        $lines = $this->renderLines();
        
        $output = '';
        foreach ($lines as $line) {
            $output .= $line . "\n";
        }
        
        return $output;
    }
}
