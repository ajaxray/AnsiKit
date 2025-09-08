<?php

declare(strict_types=1);

namespace Writers;

use Ajaxray\AnsiKit\Writers\MemoryWriter;
use PHPUnit\Framework\TestCase;

final class MemoryWriterTest extends TestCase
{
    public function testBuffer(): void
    {
        $w = new MemoryWriter();
        $w->write("abc");
        $w->write("123");
        $this->assertSame("abc123", $w->getBuffer());
        $w->clear();
        $this->assertSame("", $w->getBuffer());
    }
}
