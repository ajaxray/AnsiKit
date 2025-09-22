<?php

declare(strict_types=1);

namespace Components;

use Ajaxray\AnsiKit\Components\Banner;
use Ajaxray\AnsiKit\Writers\MemoryWriter;
use PHPUnit\Framework\TestCase;

final class BannerTest extends TestCase
{
    public function testBannerRendersBoxAroundTitleAndDetails(): void
    {
        $w = new MemoryWriter();
        $b = new Banner($w);

        $b->render('Deploy Complete', ['Everything shipped!']);

        $out = $w->getBuffer();
        $this->assertStringContainsString('╭', $out);
        $this->assertStringContainsString('╯', $out);
        $this->assertStringContainsString('Deploy Complete', $out);
        $this->assertStringContainsString('Everything shipped!', $out);
    }
}
