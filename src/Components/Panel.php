<?php

declare(strict_types=1);

namespace Ajaxray\AnsiKit\Components;

use Ajaxray\AnsiKit\AnsiTerminal;
use Ajaxray\AnsiKit\Contracts\Renderable;
use Ajaxray\AnsiKit\Contracts\WriterInterface;
use Ajaxray\AnsiKit\Support\Str;

/**
 * Panel component for creating flexible layouts with blocks.
 * Supports vertical (rows) and horizontal (columns) layouts with customizable sizing.
 * Implements Renderable to allow nesting panels within other panels.
 */
final class Panel implements Renderable
{
    // Layout constants
    public const LAYOUT_VERTICAL = 'vertical';
    public const LAYOUT_HORIZONTAL = 'horizontal';
    
    // Corner style constants
    public const CORNER_SHARP = 'sharp';
    public const CORNER_ROUNDED = 'rounded';

    private AnsiTerminal $t;
    private string $layout = self::LAYOUT_VERTICAL;
    private array $blocks = [];
    private array $sizes = []; // Custom sizes for blocks
    private bool $hasBorder = false;
    private bool $hasDividers = false;
    private string $dividerChar = '│';
    private string $cornerStyle = self::CORNER_SHARP;
    
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
        $this->t = new AnsiTerminal($writer);
    }

    /**
     * Set layout direction: Panel::LAYOUT_VERTICAL or Panel::LAYOUT_HORIZONTAL.
     */
    public function layout(string $layout): self
    {
        if (!in_array($layout, [self::LAYOUT_VERTICAL, self::LAYOUT_HORIZONTAL], true)) {
            throw new \InvalidArgumentException('Layout must be "vertical" or "horizontal"');
        }
        $this->layout = $layout;
        return $this;
    }

    /**
     * Add a block to the panel.
     * Accepts any Renderable component (PanelBlock or nested Panel).
     */
    public function addBlock(Renderable $block): self
    {
        $this->blocks[] = $block;
        return $this;
    }

    /**
     * Set custom sizes for blocks. Array of integers representing character units.
     * If not set or 0, blocks will take equal space or auto-size based on content.
     */
    public function setSizes(array $sizes): self
    {
        $this->sizes = $sizes;
        return $this;
    }

    /**
     * Enable/disable panel border.
     */
    public function border(bool $enabled = true): self
    {
        $this->hasBorder = $enabled;
        return $this;
    }

    /**
     * Enable/disable dividers between blocks.
     */
    public function dividers(bool $enabled = true, string $char = '│'): self
    {
        $this->hasDividers = $enabled;
        $this->dividerChar = $char;
        return $this;
    }

    /**
     * Set corner style: Panel::CORNER_SHARP or Panel::CORNER_ROUNDED.
     */
    public function corners(string $style): self
    {
        if (!in_array($style, [self::CORNER_SHARP, self::CORNER_ROUNDED], true)) {
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
        if ($this->cornerStyle === self::CORNER_ROUNDED) {
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
     * Render the component as an array of lines.
     * Each line is properly formatted and padded.
     *
     * @return list<string>
     */
    public function renderLines(): array
    {
        if (empty($this->blocks)) {
            return [];
        }

        if ($this->layout === self::LAYOUT_VERTICAL) {
            return $this->renderVerticalLines();
        } else {
            return $this->renderHorizontalLines();
        }
    }

    /**
     * Get the total width including any borders.
     */
    public function getTotalWidth(): int
    {
        $contentWidth = $this->getContentWidth();
        return $contentWidth + ($this->hasBorder ? 2 : 0);
    }

    /**
     * Get the total height including any borders.
     */
    public function getTotalHeight(): int
    {
        $contentHeight = $this->getContentHeight();
        return $contentHeight + ($this->hasBorder ? 2 : 0);
    }

    /**
     * Render the panel with all blocks to output.
     */
    public function render(): void
    {
        $lines = $this->renderLines();
        foreach ($lines as $line) {
            $this->t->write($line)->newline();
        }
    }

    /**
     * Render vertical layout (stacked rows) as lines.
     */
    private function renderVerticalLines(): array
    {
        $lines = [];
        $panelWidth = $this->getContentWidth();
        $corners = $this->getCornerCharacters();
        
        if ($this->hasBorder) {
            $lines[] = $corners['topLeft'] . str_repeat('─', $panelWidth) . $corners['topRight'];
        }
        
        foreach ($this->blocks as $index => $block) {
            $blockLines = $block->renderLines();
            
            foreach ($blockLines as $line) {
                // Ensure line matches panel width
                $lineWidth = Str::visibleLength($line);
                if ($lineWidth < $panelWidth) {
                    $line .= str_repeat(' ', $panelWidth - $lineWidth);
                } elseif ($lineWidth > $panelWidth) {
                    $line = substr($line, 0, $panelWidth);
                }
                
                if ($this->hasBorder) {
                    $lines[] = '│' . $line . '│';
                } else {
                    $lines[] = $line;
                }
            }
            
            // Add divider if not last block
            if ($this->hasDividers && $index < count($this->blocks) - 1) {
                if ($this->hasBorder) {
                    $lines[] = '├' . str_repeat('─', $panelWidth) . '┤';
                } else {
                    $lines[] = str_repeat('─', $panelWidth);
                }
            }
        }
        
        if ($this->hasBorder) {
            $lines[] = $corners['bottomLeft'] . str_repeat('─', $panelWidth) . $corners['bottomRight'];
        }
        
        return $lines;
    }

    /**
     * Render horizontal layout (side-by-side columns) as lines.
     */
    private function renderHorizontalLines(): array
    {
        $lines = [];
        $blockWidths = $this->calculateHorizontalWidths();
        $maxHeight = $this->getContentHeight();
        
        // Prepare all block lines
        $allBlockLines = [];
        foreach ($this->blocks as $blockIdx => $block) {
            $blockLines = $block->renderLines();
            $blockWidth = $blockWidths[$blockIdx];
            // Pad to max height
            while (count($blockLines) < $maxHeight) {
                $blockLines[] = str_repeat(' ', $blockWidth);
            }
            $allBlockLines[] = $blockLines;
        }
        
        $totalWidth = array_sum($blockWidths);
        if ($this->hasDividers) {
            $totalWidth += count($this->blocks) - 1; // Add space for dividers
        }
        
        $corners = $this->getCornerCharacters();
        if ($this->hasBorder) {
            $lines[] = $corners['topLeft'] . str_repeat('─', $totalWidth) . $corners['topRight'];
        }
        
        // Render each line
        for ($lineIdx = 0; $lineIdx < $maxHeight; $lineIdx++) {
            $lineContent = '';
            
            if ($this->hasBorder) {
                $lineContent .= '│';
            }
            
            foreach ($this->blocks as $blockIdx => $block) {
                $blockLine = $allBlockLines[$blockIdx][$lineIdx] ?? str_repeat(' ', $blockWidths[$blockIdx]);
                
                // Ensure line matches expected width
                $lineWidth = Str::visibleLength($blockLine);
                $expectedWidth = $blockWidths[$blockIdx];
                if ($lineWidth < $expectedWidth) {
                    $blockLine .= str_repeat(' ', $expectedWidth - $lineWidth);
                } elseif ($lineWidth > $expectedWidth) {
                    $blockLine = substr($blockLine, 0, $expectedWidth);
                }
                
                $lineContent .= $blockLine;
                
                // Add divider if not last block
                if ($this->hasDividers && $blockIdx < count($this->blocks) - 1) {
                    $lineContent .= $this->dividerChar;
                }
            }
            
            if ($this->hasBorder) {
                $lineContent .= '│';
            }
            
            $lines[] = $lineContent;
        }
        
        if ($this->hasBorder) {
            $lines[] = $corners['bottomLeft'] . str_repeat('─', $totalWidth) . $corners['bottomRight'];
        }
        
        return $lines;
    }

    /**
     * Get the content width excluding borders.
     * For vertical layout, returns the maximum width of all blocks.
     * For horizontal layout, sums all block widths plus dividers.
     */
    public function getContentWidth(): int
    {
        if ($this->layout === self::LAYOUT_HORIZONTAL) {
            $totalWidth = 0;
            foreach ($this->blocks as $index => $block) {
                $totalWidth += $block->getTotalWidth();
                // Add divider width if not last block
                if ($this->hasDividers && $index < count($this->blocks) - 1) {
                    $totalWidth += 1;
                }
            }
            return $totalWidth;
        } else {
            // Vertical layout: max width of all blocks
            $maxWidth = 0;
            foreach ($this->blocks as $block) {
                $maxWidth = max($maxWidth, $block->getTotalWidth());
            }
            return $maxWidth;
        }
    }

    /**
     * Get the content height excluding borders.
     * For vertical layout, sums all block heights plus dividers.
     * For horizontal layout, returns the maximum height of all blocks.
     */
    public function getContentHeight(): int
    {
        if ($this->layout === self::LAYOUT_VERTICAL) {
            $totalHeight = 0;
            foreach ($this->blocks as $index => $block) {
                $totalHeight += $block->getTotalHeight();
                // Add divider height if not last block
                if ($this->hasDividers && $index < count($this->blocks) - 1) {
                    $totalHeight += 1;
                }
            }
            return $totalHeight;
        } else {
            // Horizontal layout: max height of all blocks
            $maxHeight = 0;
            foreach ($this->blocks as $block) {
                $maxHeight = max($maxHeight, $block->getTotalHeight());
            }
            return $maxHeight;
        }
    }

    /**
     * Calculate widths for horizontal layout.
     */
    private function calculateHorizontalWidths(): array
    {
        $widths = [];
        
        foreach ($this->blocks as $index => $block) {
            if (isset($this->sizes[$index]) && $this->sizes[$index] > 0) {
                $widths[] = $this->sizes[$index];
            } else {
                // Use getTotalWidth for nested panels with borders
                $widths[] = $block->getTotalWidth();
            }
        }
        
        return $widths;
    }
}
