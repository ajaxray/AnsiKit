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
}
