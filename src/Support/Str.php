<?php

declare(strict_types=1);

namespace Ajaxray\AnsiKit\Support;

use SoloTerm\Grapheme\Grapheme;

/**
 * Public string helpers that are ANSI-aware.
 */
final class Str
{
    /**
     * Strip ANSI SGR escape sequences from a string.
     */
    public static function stripAnsi(string $s): string
    {
        return (string) preg_replace('/\e\[[0-9;]*m/', '', $s);
    }

    /**
     * Length of a string as it would be visibly rendered (ANSI ignored).
     */
    public static function visibleLength(string $s): int
    {
        $noAnsi = self::stripAnsi($s);
        return Grapheme::wcwidth($noAnsi);
    }
}

