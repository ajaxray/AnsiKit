<?php

declare(strict_types=1);

namespace Components;

use Ajaxray\AnsiKit\Components\Progressbar;
use Ajaxray\AnsiKit\Writers\MemoryWriter;
use Ajaxray\AnsiKit\AnsiTerminal;
use PHPUnit\Framework\TestCase;

final class ProgressbarTest extends TestCase
{
    public function testBasicProgressbarRender(): void
    {
        $w = new MemoryWriter();
        $progressbar = new Progressbar($w);

        $progressbar->render(50, 100);
        $out = $w->getBuffer();

        // Should contain default borders
        $this->assertStringContainsString('[', $out);
        $this->assertStringContainsString(']', $out);
        
        // Should contain fill and empty characters
        $this->assertStringContainsString('█', $out);
        $this->assertStringContainsString('░', $out);
        
        // Should show percentage and count
        $this->assertStringContainsString('50%', $out);
        $this->assertStringContainsString('(50/100)', $out);
    }

    public function testProgressbarWithLabel(): void
    {
        $w = new MemoryWriter();
        $progressbar = new Progressbar($w);

        $progressbar->render(25, 100, 'Loading files');
        $out = $w->getBuffer();

        $this->assertStringContainsString('Loading files', $out);
        $this->assertStringContainsString('25%', $out);
        $this->assertStringContainsString('(25/100)', $out);
    }

    public function testProgressbarCustomWidth(): void
    {
        $w = new MemoryWriter();
        $progressbar = new Progressbar($w);

        $progressbar->width(20)->render(10, 20);
        $out = $w->getBuffer();

        // With width 20 and 50% progress, should have 10 fill chars and 10 empty chars
        $fillCount = substr_count($out, '█');
        $emptyCount = substr_count($out, '░');
        
        $this->assertSame(10, $fillCount);
        $this->assertSame(10, $emptyCount);
    }

    public function testProgressbarCustomCharacters(): void
    {
        $w = new MemoryWriter();
        $progressbar = new Progressbar($w);

        $progressbar->chars('▓', '▒')->render(50, 100);
        $out = $w->getBuffer();

        $this->assertStringContainsString('▓', $out);
        $this->assertStringContainsString('▒', $out);
        $this->assertStringNotContainsString('█', $out);
        $this->assertStringNotContainsString('░', $out);
    }

    public function testProgressbarCustomBorders(): void
    {
        $w = new MemoryWriter();
        $progressbar = new Progressbar($w);

        $progressbar->borders('|', '|')->render(50, 100);
        $out = $w->getBuffer();

        $this->assertStringContainsString('|', $out);
        // Used in ASCII sequence characters
        //$this->assertStringNotContainsString("[", $out);
        $this->assertStringNotContainsString("]", $out);
    }

    public function testProgressbarDisplayOptions(): void
    {
        $w = new MemoryWriter();
        $progressbar = new Progressbar($w);

        // Test with percentage only
        $progressbar->display(true, false)->render(50, 100);
        $out = $w->getBuffer();

        $this->assertStringContainsString('50%', $out);
        $this->assertStringNotContainsString('(50/100)', $out);

        // Reset buffer and test with count only
        $w->clear();
        $progressbar->display(false, true)->render(50, 100);
        $out = $w->getBuffer();

        $this->assertStringNotContainsString('50%', $out);
        $this->assertStringContainsString('(50/100)', $out);

        // Reset buffer and test with neither
        $w->clear();
        $progressbar->display(false, false)->render(50, 100);
        $out = $w->getBuffer();

        $this->assertStringNotContainsString('50%', $out);
        $this->assertStringNotContainsString('(50/100)', $out);
    }

    public function testProgressbarEdgeCases(): void
    {
        $w = new MemoryWriter();
        $progressbar = new Progressbar($w);

        // Test 0% progress
        $progressbar->render(0, 100);
        $out = $w->getBuffer();
        $this->assertStringContainsString('0%', $out);
        $this->assertStringContainsString('(0/100)', $out);

        // Test 100% progress
        $w->clear();
        $progressbar->render(100, 100);
        $out = $w->getBuffer();
        $this->assertStringContainsString('100%', $out);
        $this->assertStringContainsString('(100/100)', $out);

        // Test progress over total (should be clamped)
        $w->clear();
        $progressbar->render(150, 100);
        $out = $w->getBuffer();
        $this->assertStringContainsString('100%', $out);
        $this->assertStringContainsString('(100/100)', $out);

        // Test negative progress (should be clamped to 0)
        $w->clear();
        $progressbar->render(-10, 100);
        $out = $w->getBuffer();
        $this->assertStringContainsString('0%', $out);
        $this->assertStringContainsString('(0/100)', $out);
    }

    public function testProgressbarStyling(): void
    {
        $w = new MemoryWriter();
        $progressbar = new Progressbar($w);

        $progressbar
            ->barStyle([AnsiTerminal::FG_GREEN])
            ->percentageStyle([AnsiTerminal::TEXT_BOLD])
            ->labelStyle([AnsiTerminal::FG_CYAN])
            ->render(50, 100, 'Processing');

        $out = $w->getBuffer();

        // Should contain ANSI escape sequences for styling
        $this->assertStringContainsString("\033[32m", $out); // FG_GREEN
        $this->assertStringContainsString("\033[1m", $out);  // TEXT_BOLD
        $this->assertStringContainsString("\033[36m", $out); // FG_CYAN
        $this->assertStringContainsString("\033[0m", $out);  // RESET
    }

    public function testRenderLine(): void
    {
        $w = new MemoryWriter();
        $progressbar = new Progressbar($w);

        $progressbar->renderLine(50, 100, 'Loading');
        $out = $w->getBuffer();

        $this->assertStringContainsString('Loading', $out);
        $this->assertStringContainsString('50%', $out);
        $this->assertStringEndsWith(PHP_EOL, $out);
    }

    public function testRenderInPlace(): void
    {
        $w = new MemoryWriter();
        $progressbar = new Progressbar($w);

        $progressbar->renderInPlace(50, 100, 'Processing');
        $out = $w->getBuffer();

        $this->assertStringContainsString('Processing', $out);
        $this->assertStringContainsString('50%', $out);
        $this->assertStringContainsString("\r", $out); // Should contain carriage returns
    }

    public function testProgressbarAccuracy(): void
    {
        $w = new MemoryWriter();
        $progressbar = new Progressbar($w);

        // Test various percentages for accuracy
        $testCases = [
            [0, 100, 0],
            [25, 100, 25],
            [50, 100, 50],
            [75, 100, 75],
            [100, 100, 100],
            [33, 100, 33],
            [66, 100, 66],
        ];

        foreach ($testCases as [$current, $total, $expectedPercentage]) {
            $w->clear();
            $progressbar->render($current, $total);
            $out = $w->getBuffer();
            
            $this->assertStringContainsString("{$expectedPercentage}%", $out);
            $this->assertStringContainsString("({$current}/{$total})", $out);
        }
    }

    public function testMinimumWidth(): void
    {
        $w = new MemoryWriter();
        $progressbar = new Progressbar($w);

        // Test that width cannot be less than 1
        $progressbar->width(0)->render(50, 100);
        $out = $w->getBuffer();

        // Should still render something (minimum width should be enforced)
        $this->assertStringContainsString('[', $out);
        $this->assertStringContainsString(']', $out);
    }
}
