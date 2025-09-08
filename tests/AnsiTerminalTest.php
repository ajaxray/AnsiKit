<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Ajaxray\AnsiKit\AnsiTerminal;
use Ajaxray\AnsiKit\Writers\MemoryWriter;

final class AnsiTerminalTest extends TestCase
{
    public function testClearScreenAndCursorHome(): void
    {
        $w = new MemoryWriter();
        $t = new AnsiTerminal($w);

        $t->clearScreen()->cursorHome();
        $out = $w->getBuffer();

        $this->assertStringContainsString("\033[2J", $out); // CSI 2J
        $this->assertStringContainsString("\033[H", $out);  // CSI H
    }

    public function testFgBgTruecolor(): void
    {
        $w = new MemoryWriter();
        $t = new AnsiTerminal($w);

        $t->fgRGB(120, 200, 255)->write("x")->bgRGB(10, 20, 30)->write("y")->reset();
        $out = $w->getBuffer();

        $this->assertStringContainsString("\033[38;2;120;200;255m", $out);
        $this->assertStringContainsString("\033[48;2;10;20;30m", $out);
        $this->assertStringContainsString("\033[0m", $out);
    }

    public function testWriteStyled(): void
    {
        $w = new MemoryWriter();
        $t = new AnsiTerminal($w);
        $t->writeStyled("HELLO", [AnsiTerminal::TEXT_BOLD, AnsiTerminal::FG_RED]);

        $out = $w->getBuffer();
        $this->assertStringContainsString("\033[1;31mHELLO\033[0m", $out);
    }
}
