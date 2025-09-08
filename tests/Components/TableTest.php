<?php

declare(strict_types=1);

namespace Components;

use Ajaxray\AnsiKit\Components\Table;
use Ajaxray\AnsiKit\Writers\MemoryWriter;
use PHPUnit\Framework\TestCase;

final class TableTest extends TestCase
{
    public function testBasicTableRendersBorders(): void
    {
        $w = new MemoryWriter();
        $table = new Table($w);

        $table->setHeaders('Col A', 'Col B')
            ->addRow('1', '2')
            ->addRow('3', '4')
            ->render();

        $out = $w->getBuffer();

        $this->assertStringContainsString('┌', $out);
        $this->assertStringContainsString('┐', $out);
        $this->assertStringContainsString('└', $out);
        $this->assertStringContainsString('┘', $out);
        $this->assertStringContainsString('│', $out);
        $this->assertStringContainsString('Col A', $out);
        $this->assertStringContainsString('Col B', $out);
    }
}
