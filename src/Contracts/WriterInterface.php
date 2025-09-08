<?php

declare(strict_types=1);

namespace Ajaxray\AnsiKit\Contracts;

interface WriterInterface
{
    /** Write raw bytes (no newline). Should return number of bytes written. */
    public function write(string $bytes): int;
}