<?php

declare(strict_types=1);

namespace Support;

use Ajaxray\AnsiKit\AnsiTerminal;
use Ajaxray\AnsiKit\Support\Util;
use Ajaxray\AnsiKit\Writers\MemoryWriter;
use PHPUnit\Framework\TestCase;

final class UtilTest extends TestCase
{
    public function testBeepWritesBellSequenceUsingTerminal(): void
    {
        $writer = new MemoryWriter();
        $terminal = new AnsiTerminal($writer);

        Util::beep($terminal);

        $this->assertSame("\007", $writer->getBuffer());
    }

    public function testBeepWritesBellSequenceToStdoutWhenNoTerminal(): void
    {
        ob_start();
        Util::beep();
        $output = ob_get_clean();

        $this->assertSame("\007", $output);
    }

    public function testSetTerminalTabTitleWritesSequenceUsingTerminal(): void
    {
        $writer = new MemoryWriter();
        $terminal = new AnsiTerminal($writer);

        Util::setTerminalTabTitle('AnsiKit Demo', $terminal);

        $this->assertSame(AnsiTerminal::ESC . ']0;AnsiKit Demo' . "\007", $writer->getBuffer());
    }

    public function testSetTerminalTabTitleWritesSequenceToStdoutWhenNoTerminal(): void
    {
        ob_start();
        Util::setTerminalTabTitle('AnsiKit Demo');
        $output = ob_get_clean();

        $this->assertSame(AnsiTerminal::ESC . ']0;AnsiKit Demo' . "\007", $output);
    }
}
