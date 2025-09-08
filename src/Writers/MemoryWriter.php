<?php

declare(strict_types=1);

namespace Ajaxray\AnsiKit\Writers;

use Ajaxray\AnsiKit\Contracts\WriterInterface;

final class MemoryWriter implements WriterInterface
{
    private string $buffer = '';

    public function write(string $bytes): int
    {
        $this->buffer .= $bytes;
        return \strlen($bytes);
    }

    public function getBuffer(): string
    {
        return $this->buffer;
    }

    public function clear(): void
    {
        $this->buffer = '';
    }
}