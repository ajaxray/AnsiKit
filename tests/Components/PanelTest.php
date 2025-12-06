<?php

declare(strict_types=1);

namespace Ajaxray\AnsiKit\Tests\Components;

use Ajaxray\AnsiKit\Components\Panel;
use Ajaxray\AnsiKit\Components\PanelBlock;
use Ajaxray\AnsiKit\Writers\MemoryWriter;
use PHPUnit\Framework\TestCase;

final class PanelTest extends TestCase
{
    public function testVerticalLayoutWithoutBorder(): void
    {
        $writer = new MemoryWriter();
        $panel = new Panel($writer);
        
        $block1 = (new PanelBlock())->content('Block 1')->width(20);
        $block2 = (new PanelBlock())->content('Block 2')->width(20);
        
        $panel->layout(Panel::LAYOUT_VERTICAL)
            ->addBlock($block1)
            ->addBlock($block2)
            ->render();
        
        $output = $writer->getBuffer();
        
        $this->assertStringContainsString('Block 1', $output);
        $this->assertStringContainsString('Block 2', $output);
    }

    public function testVerticalLayoutWithBorder(): void
    {
        $writer = new MemoryWriter();
        $panel = new Panel($writer);
        
        $block1 = (new PanelBlock())->content('Block 1')->width(20);
        $block2 = (new PanelBlock())->content('Block 2')->width(20);
        
        $panel->layout(Panel::LAYOUT_VERTICAL)
            ->border(true)
            ->addBlock($block1)
            ->addBlock($block2)
            ->render();
        
        $output = $writer->getBuffer();
        
        $this->assertStringContainsString('┌', $output);
        $this->assertStringContainsString('└', $output);
        $this->assertStringContainsString('│', $output);
    }

    public function testVerticalLayoutWithDividers(): void
    {
        $writer = new MemoryWriter();
        $panel = new Panel($writer);
        
        $block1 = (new PanelBlock())->content('Block 1')->width(20);
        $block2 = (new PanelBlock())->content('Block 2')->width(20);
        
        $panel->layout(Panel::LAYOUT_VERTICAL)
            ->border(true)
            ->dividers(true)
            ->addBlock($block1)
            ->addBlock($block2)
            ->render();
        
        $output = $writer->getBuffer();
        
        $this->assertStringContainsString('├', $output);
        $this->assertStringContainsString('┤', $output);
    }

    public function testHorizontalLayoutWithoutBorder(): void
    {
        $writer = new MemoryWriter();
        $panel = new Panel($writer);
        
        $block1 = (new PanelBlock())->content('Left')->width(15);
        $block2 = (new PanelBlock())->content('Right')->width(15);
        
        $panel->layout(Panel::LAYOUT_HORIZONTAL)
            ->addBlock($block1)
            ->addBlock($block2)
            ->render();
        
        $output = $writer->getBuffer();
        
        $this->assertStringContainsString('Left', $output);
        $this->assertStringContainsString('Right', $output);
    }

    public function testHorizontalLayoutWithBorder(): void
    {
        $writer = new MemoryWriter();
        $panel = new Panel($writer);
        
        $block1 = (new PanelBlock())->content('Left')->width(15);
        $block2 = (new PanelBlock())->content('Right')->width(15);
        
        $panel->layout(Panel::LAYOUT_HORIZONTAL)
            ->border(true)
            ->addBlock($block1)
            ->addBlock($block2)
            ->render();
        
        $output = $writer->getBuffer();
        
        $this->assertStringContainsString('┌', $output);
        $this->assertStringContainsString('└', $output);
    }

    public function testHorizontalLayoutWithDividers(): void
    {
        $writer = new MemoryWriter();
        $panel = new Panel($writer);
        
        $block1 = (new PanelBlock())->content('Left')->width(15);
        $block2 = (new PanelBlock())->content('Right')->width(15);
        
        $panel->layout(Panel::LAYOUT_HORIZONTAL)
            ->dividers(true)
            ->addBlock($block1)
            ->addBlock($block2)
            ->render();
        
        $output = $writer->getBuffer();
        
        $this->assertStringContainsString('│', $output);
    }

    public function testPanelBlockAsWriter(): void
    {
        $writer = new MemoryWriter();
        $block = new PanelBlock($writer);
        
        $block->write('Test content');
        
        $this->assertStringContainsString('Test content', $writer->getBuffer());
    }

    public function testPanelBlockWithBorder(): void
    {
        $writer = new MemoryWriter();
        $block = (new PanelBlock($writer))
            ->content('Test')
            ->width(10)
            ->border(true);
        
        $output = $block->render();
        
        $this->assertStringContainsString('┌', $output);
        $this->assertStringContainsString('└', $output);
        $this->assertStringContainsString('│', $output);
    }

    public function testPanelBlockWordWrap(): void
    {
        $writer = new MemoryWriter();
        $block = (new PanelBlock($writer))
            ->content('This is a very long text that should wrap')
            ->width(15)
            ->overflow(PanelBlock::OVERFLOW_WORDWRAP);
        
        $lines = $block->renderLines();
        
        $this->assertGreaterThan(1, count($lines));
    }

    public function testCustomSizes(): void
    {
        $writer = new MemoryWriter();
        $panel = new Panel($writer);
        
        $block1 = (new PanelBlock())->content('Small')->width(10);
        $block2 = (new PanelBlock())->content('Large')->width(30);
        
        $panel->layout(Panel::LAYOUT_HORIZONTAL)
            ->setSizes([10, 30])
            ->addBlock($block1)
            ->addBlock($block2)
            ->render();
        
        $output = $writer->getBuffer();
        
        $this->assertStringContainsString('Small', $output);
        $this->assertStringContainsString('Large', $output);
    }

    public function testInvalidLayoutThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        
        $panel = new Panel();
        $panel->layout('invalid');
    }

    public function testInvalidOverflowThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        
        $block = new PanelBlock();
        $block->overflow('invalid');
    }

    public function testPanelCornerStyleValidation(): void
    {
        $panel = new Panel();
        
        // Test valid corner styles
        $this->assertInstanceOf(Panel::class, $panel->corners(Panel::CORNER_SHARP));
        $this->assertInstanceOf(Panel::class, $panel->corners(Panel::CORNER_ROUNDED));
        
        // Test invalid corner style
        $this->expectException(\InvalidArgumentException::class);
        $panel->corners('invalid');
    }

    public function testPanelBlockCornerStyleValidation(): void
    {
        $block = new PanelBlock();
        
        // Test valid corner styles
        $this->assertInstanceOf(PanelBlock::class, $block->corners(Panel::CORNER_SHARP));
        $this->assertInstanceOf(PanelBlock::class, $block->corners(Panel::CORNER_ROUNDED));
        
        // Test invalid corner style
        $this->expectException(\InvalidArgumentException::class);
        $block->corners('invalid');
    }

    public function testPanelRoundedCorners(): void
    {
        $writer = new MemoryWriter();
        $panel = new Panel($writer);
        
        $block = (new PanelBlock())->content('Test')->width(20);
        
        $panel->layout(Panel::LAYOUT_VERTICAL)
            ->border(true)
            ->corners(Panel::CORNER_ROUNDED)
            ->addBlock($block)
            ->render();
        
        $output = $writer->getBuffer();
        
        // Should contain rounded corner characters
        $this->assertStringContainsString('╭', $output);
        $this->assertStringContainsString('╮', $output);
        $this->assertStringContainsString('╰', $output);
        $this->assertStringContainsString('╯', $output);
        
        // Should not contain sharp corner characters
        $this->assertStringNotContainsString('┌', $output);
        $this->assertStringNotContainsString('┐', $output);
        $this->assertStringNotContainsString('└', $output);
        $this->assertStringNotContainsString('┘', $output);
    }

    public function testPanelBlockRoundedCorners(): void
    {
        $block = (new PanelBlock())
            ->content('Test')
            ->width(20)
            ->border(true)
            ->corners(Panel::CORNER_ROUNDED);
        
        $output = $block->render();
        
        // Should contain rounded corner characters
        $this->assertStringContainsString('╭', $output);
        $this->assertStringContainsString('╮', $output);
        $this->assertStringContainsString('╰', $output);
        $this->assertStringContainsString('╯', $output);
        
        // Should not contain sharp corner characters
        $this->assertStringNotContainsString('┌', $output);
        $this->assertStringNotContainsString('┐', $output);
        $this->assertStringNotContainsString('└', $output);
        $this->assertStringNotContainsString('┘', $output);
    }

    public function testPanelDefaultCornerStyle(): void
    {
        $writer = new MemoryWriter();
        $panel = new Panel($writer);
        
        $block = (new PanelBlock())->content('Test')->width(20);
        
        $panel->layout(Panel::LAYOUT_VERTICAL)
            ->border(true)
            ->addBlock($block)
            ->render();
        
        $output = $writer->getBuffer();
        
        // Should contain sharp corner characters (default)
        $this->assertStringContainsString('┌', $output);
        $this->assertStringContainsString('┐', $output);
        $this->assertStringContainsString('└', $output);
        $this->assertStringContainsString('┘', $output);
        
        // Should not contain rounded corner characters
        $this->assertStringNotContainsString('╭', $output);
        $this->assertStringNotContainsString('╮', $output);
        $this->assertStringNotContainsString('╰', $output);
        $this->assertStringNotContainsString('╯', $output);
    }

    public function testNestedHorizontalPanelInVerticalPanel(): void
    {
        $writer = new MemoryWriter();
        $mainPanel = new Panel($writer);
        
        // Create nested horizontal panel
        $nestedPanel = new Panel();
        $nestedPanel->layout(Panel::LAYOUT_HORIZONTAL)
            ->border(true)
            ->dividers(true)
            ->addBlock((new PanelBlock())->content('Col 1')->width(10))
            ->addBlock((new PanelBlock())->content('Col 2')->width(10));
        
        // Add to main vertical panel
        $mainPanel->layout(Panel::LAYOUT_VERTICAL)
            ->border(true)
            ->dividers(true)
            ->addBlock((new PanelBlock())->content('Header'))
            ->addBlock($nestedPanel)
            ->addBlock((new PanelBlock())->content('Footer'))
            ->render();
        
        $output = $writer->getBuffer();
        
        // Check that all content is present
        $this->assertStringContainsString('Header', $output);
        $this->assertStringContainsString('Col 1', $output);
        $this->assertStringContainsString('Col 2', $output);
        $this->assertStringContainsString('Footer', $output);
        
        // Check for nested panel borders
        $this->assertStringContainsString('┌', $output);
        $this->assertStringContainsString('└', $output);
    }

    public function testNestedVerticalPanelsInHorizontalPanel(): void
    {
        $writer = new MemoryWriter();
        $mainPanel = new Panel($writer);
        
        // Create nested vertical panels
        $leftPanel = new Panel();
        $leftPanel->layout(Panel::LAYOUT_VERTICAL)
            ->border(true)
            ->dividers(true)
            ->addBlock((new PanelBlock())->content('Menu')->width(10))
            ->addBlock((new PanelBlock())->content('Home')->width(10));
        
        $rightPanel = new Panel();
        $rightPanel->layout(Panel::LAYOUT_VERTICAL)
            ->border(true)
            ->dividers(true)
            ->addBlock((new PanelBlock())->content('Content')->width(15))
            ->addBlock((new PanelBlock())->content('More')->width(15));
        
        // Add to main horizontal panel
        $mainPanel->layout(Panel::LAYOUT_HORIZONTAL)
            ->border(true)
            ->dividers(true)
            ->addBlock($leftPanel)
            ->addBlock($rightPanel)
            ->render();
        
        $output = $writer->getBuffer();
        
        // Check that all content is present
        $this->assertStringContainsString('Menu', $output);
        $this->assertStringContainsString('Home', $output);
        $this->assertStringContainsString('Content', $output);
        $this->assertStringContainsString('More', $output);
    }

    public function testThreeLevelNestedPanels(): void
    {
        $writer = new MemoryWriter();
        
        // Level 3 - innermost panel
        $level3 = new Panel();
        $level3->layout(Panel::LAYOUT_HORIZONTAL)
            ->border(true)
            ->addBlock((new PanelBlock())->content('L3-A')->width(5))
            ->addBlock((new PanelBlock())->content('L3-B')->width(5));
        
        // Level 2 - middle panel
        $level2 = new Panel();
        $level2->layout(Panel::LAYOUT_VERTICAL)
            ->border(true)
            ->dividers(true)
            ->addBlock((new PanelBlock())->content('L2 Header'))
            ->addBlock($level3)
            ->addBlock((new PanelBlock())->content('L2 Footer'));
        
        // Level 1 - outer panel
        $level1 = new Panel($writer);
        $level1->layout(Panel::LAYOUT_VERTICAL)
            ->border(true)
            ->dividers(true)
            ->addBlock((new PanelBlock())->content('L1 Top'))
            ->addBlock($level2)
            ->addBlock((new PanelBlock())->content('L1 Bottom'))
            ->render();
        
        $output = $writer->getBuffer();
        
        // Check that all levels are present
        $this->assertStringContainsString('L1 Top', $output);
        $this->assertStringContainsString('L1 Bottom', $output);
        $this->assertStringContainsString('L2 Header', $output);
        $this->assertStringContainsString('L2 Footer', $output);
        $this->assertStringContainsString('L3-A', $output);
        $this->assertStringContainsString('L3-B', $output);
    }

    public function testNestedPanelImplementsRenderable(): void
    {
        $panel = new Panel();
        $panel->layout(Panel::LAYOUT_VERTICAL)
            ->addBlock((new PanelBlock())->content('Test')->width(10));
        
        // Test that Panel implements Renderable interface methods
        $this->assertIsArray($panel->renderLines());
        $this->assertIsInt($panel->getTotalWidth());
        $this->assertIsInt($panel->getTotalHeight());
        $this->assertIsInt($panel->getContentWidth());
    }

    public function testNestedPanelWidthCalculation(): void
    {
        // Horizontal panel width should be sum of blocks + dividers + borders
        $panel = new Panel();
        $panel->layout(Panel::LAYOUT_HORIZONTAL)
            ->border(true)
            ->dividers(true)
            ->addBlock((new PanelBlock())->content('A')->width(10))
            ->addBlock((new PanelBlock())->content('B')->width(10))
            ->addBlock((new PanelBlock())->content('C')->width(10));
        
        // Width: 10 + 10 + 10 (blocks) + 2 (dividers) = 32 content
        // Total: 32 + 2 (borders) = 34
        $this->assertEquals(32, $panel->getContentWidth());
        $this->assertEquals(34, $panel->getTotalWidth());
    }

    public function testNestedPanelHeightCalculation(): void
    {
        // Vertical panel height should be sum of blocks + dividers + borders
        $panel = new Panel();
        $panel->layout(Panel::LAYOUT_VERTICAL)
            ->border(true)
            ->dividers(true)
            ->addBlock((new PanelBlock())->content('A')->width(10))
            ->addBlock((new PanelBlock())->content('B')->width(10))
            ->addBlock((new PanelBlock())->content('C')->width(10));
        
        // Height: 1 + 1 + 1 (blocks) + 2 (dividers) = 5 content
        // Total: 5 + 2 (borders) = 7
        $this->assertEquals(5, $panel->getContentHeight());
        $this->assertEquals(7, $panel->getTotalHeight());
    }
}
