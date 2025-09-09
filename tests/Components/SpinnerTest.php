<?php

declare(strict_types=1);

namespace Components;

use Ajaxray\AnsiKit\Components\Spinner;
use PHPUnit\Framework\TestCase;

final class SpinnerTest extends TestCase
{
    public function testDefaultVariantIsDotsAndCycles(): void
    {
        $s = new Spinner();
        $frames = $s->getFrames();

        $this->assertNotEmpty($frames, 'Default frames should not be empty');
        $this->assertCount(10, $frames, 'Default dotted spinner should have 10 frames');
        $this->assertContains('⠋', $frames);

        // Capture a full cycle using next()
        $seen = [];
        for ($i = 0; $i < count($frames); $i++) {
            $seen[] = $s->next();
        }
        $this->assertSame($frames, $seen, 'next() should iterate frames in order');

        // After a full cycle, next() should start from the beginning
        $this->assertSame($frames[0], $s->next());
    }

    public function testAsciiVariantFramesAndOrder(): void
    {
        $s = new Spinner(Spinner::ASCII);
        $frames = $s->getFrames();

        $this->assertSame(['|', '/', '-', '\\'], $frames);

        $this->assertSame('|', $s->next());
        $this->assertSame('/', $s->next());
        $this->assertSame('-', $s->next());
        $this->assertSame('\\', $s->next());
        $this->assertSame('|', $s->next(), 'Should loop after last frame');
    }

    public function testSetFramesAndReset(): void
    {
        $s = new Spinner();
        $s->setFrames(['a', 'b', 'c']);

        $this->assertSame(['a', 'b', 'c'], $s->getFrames());
        $this->assertSame('a', $s->next());
        $this->assertSame('b', $s->next());

        // Reset should take us back to the first frame
        $s->reset();
        $this->assertSame('a', $s->next());
    }

    public function testSetFramesRejectsEmpty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new Spinner())->setFrames([]);
    }

    public function testFrameAtDeterministicAndNegativeSupport(): void
    {
        $s = new Spinner(Spinner::ASCII); // frames: | / - \

        $this->assertSame('|', $s->frameAt(0));
        $this->assertSame('/', $s->frameAt(1));
        $this->assertSame('-', $s->frameAt(2));
        $this->assertSame('\\', $s->frameAt(3));
        $this->assertSame('|', $s->frameAt(4), 'wrap around');

        // Negative indexing wraps from the end
        $this->assertSame('\\', $s->frameAt(-1));
        $this->assertSame('-', $s->frameAt(-2));
        $this->assertSame('/', $s->frameAt(-3));
        $this->assertSame('|', $s->frameAt(-4));
    }
}

