<?php

declare(strict_types=1);

namespace Ajaxray\AnsiKit\Components;

use Ajaxray\AnsiKit\AnsiTerminal;
use Ajaxray\AnsiKit\Contracts\WriterInterface;
use Ajaxray\AnsiKit\Support\Str;

/**
 * Panel component for creating flexible layouts with blocks.
 * Supports vertical (rows) and horizontal (columns) layouts with customizable sizing.
 */
final class Panel
{
    private AnsiTerminal $t;
    private string $layout = 'vertical'; // 'vertical' or 'horizontal'
    private array $blocks = [];
    private array $sizes = []; // Custom sizes for blocks
    private bool $hasBorder = false;
    private bool $hasDividers = false;
    private string $dividerChar = '│';
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
        $this->t = new AnsiTerminal($writer);
    }

    /**
     * Set layout direction: 'vertical' (rows) or 'horizontal' (columns).
     */
    public function layout(string $layout): self
    {
        if (!in_array($layout, ['vertical', 'horizontal'], true)) {
            throw new \InvalidArgumentException('Layout must be "vertical" or "horizontal"');
        }
        $this->layout = $layout;
        return $this;
    }

    /**
     * Add a block to the panel.
     */
    public function addBlock(PanelBlock $block): self
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
     * Render the panel with all blocks.
     */
    public function render(): void
    {
        if (empty($this->blocks)) {
            return;
        }

        if ($this->layout === 'vertical') {
            $this->renderVertical();
        } else {
            $this->renderHorizontal();
        }
    }

    /**
     * Render vertical layout (stacked rows).
     */
    private function renderVertical(): void
    {
        $panelWidth = $this->getPanelWidth();
        $corners = $this->getCornerCharacters();
        
        if ($this->hasBorder) {
            $this->t->write($corners['topLeft'] . str_repeat('─', $panelWidth) . $corners['topRight'])->newline();
        }
        
        foreach ($this->blocks as $index => $block) {
            $lines = $block->renderLines();
            
            foreach ($lines as $line) {
                // Pad line to panel width to ensure proper alignment
                $lineWidth = Str::visibleLength($line);
                $paddedLine = $line . str_repeat(' ', $panelWidth - $lineWidth);
                
                if ($this->hasBorder) {
                    $this->t->write('│' . $paddedLine . '│')->newline();
                } else {
                    $this->t->write($paddedLine)->newline();
                }
            }
            
            // Add divider if not last block
            if ($this->hasDividers && $index < count($this->blocks) - 1) {
                if ($this->hasBorder) {
                    $this->t->write('├' . str_repeat('─', $panelWidth) . '┤')->newline();
                } else {
                    $this->t->write(str_repeat('─', $panelWidth))->newline();
                }
            }
        }
        
        if ($this->hasBorder) {
            $this->t->write($corners['bottomLeft'] . str_repeat('─', $panelWidth) . $corners['bottomRight'])->newline();
        }
    }

    /**
     * Render horizontal layout (side-by-side columns).
     */
    private function renderHorizontal(): void
    {
        $blockWidths = $this->calculateHorizontalWidths();
        $maxHeight = $this->getPanelHeight();
        
        // Prepare all block lines
        $allBlockLines = [];
        foreach ($this->blocks as $block) {
            $lines = $block->renderLines();
            // Pad to max height
            while (count($lines) < $maxHeight) {
                $lines[] = str_repeat(' ', $block->getContentWidth());
            }
            $allBlockLines[] = $lines;
        }
        
        $totalWidth = array_sum($blockWidths);
        if ($this->hasDividers) {
            $totalWidth += count($this->blocks) - 1; // Add space for dividers
        }
        
        $corners = $this->getCornerCharacters();
        if ($this->hasBorder) {
            $this->t->write($corners['topLeft'] . str_repeat('─', $totalWidth) . $corners['topRight'])->newline();
        }
        
        // Render each line
        for ($lineIdx = 0; $lineIdx < $maxHeight; $lineIdx++) {
            $lineContent = '';
            
            if ($this->hasBorder) {
                $lineContent .= '│';
            }
            
            foreach ($this->blocks as $blockIdx => $block) {
                $blockLine = $allBlockLines[$blockIdx][$lineIdx] ?? str_repeat(' ', $blockWidths[$blockIdx]);
                $lineContent .= $blockLine;
                
                // Add divider if not last block
                if ($this->hasDividers && $blockIdx < count($this->blocks) - 1) {
                    $lineContent .= $this->dividerChar;
                }
            }
            
            if ($this->hasBorder) {
                $lineContent .= '│';
            }
            
            $this->t->write($lineContent)->newline();
        }
        
        if ($this->hasBorder) {
            $this->t->write($corners['bottomLeft'] . str_repeat('─', $totalWidth) . $corners['bottomRight'])->newline();
        }
    }

    /**
     * Get panel width for vertical layout.
     */
    private function getPanelWidth(): int
    {
        $maxWidth = 0;
        foreach ($this->blocks as $block) {
            $maxWidth = max($maxWidth, $block->getTotalWidth());
        }
        return $maxWidth;
    }

    /**
     * Get panel height for horizontal layout.
     */
    private function getPanelHeight(): int
    {
        $maxHeight = 0;
        foreach ($this->blocks as $block) {
            $maxHeight = max($maxHeight, $block->getTotalHeight());
        }
        return $maxHeight;
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
                $widths[] = $block->getContentWidth();
            }
        }
        
        return $widths;
    }
}
