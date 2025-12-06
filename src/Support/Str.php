<?php

declare(strict_types=1);

namespace Ajaxray\AnsiKit\Support;

use SoloTerm\Grapheme\Grapheme;

/**
 * Public string helpers that are ANSI-aware.
 */
final class Str
{
    /** Standard emoji width: emojis = 2 columns (WezTerm, Mac Terminal, most terminals) */
    public const EMOJI_WIDTH_STANDARD = 1;

    /** Narrow emoji width: emojis = 1 column (PHPStorm, some IDEs) */
    public const EMOJI_WIDTH_NARROW = 2;

    /** Auto-detect emoji width based on terminal environment */
    public const EMOJI_WIDTH_AUTO = 3;

    /**
     * Current emoji width calculation mode.
     */
    private static int $emojiWidthMode = self::EMOJI_WIDTH_STANDARD;
    /**
     * Strip ANSI SGR escape sequences from a string.
     */
    public static function stripAnsi(string $s): string
    {
        return (string) preg_replace('/\e\[[0-9;]*m/', '', $s);
    }

    /**
     * Set emoji width calculation mode.
     *
     * @param int $mode One of EMOJI_WIDTH_STANDARD, EMOJI_WIDTH_NARROW, or EMOJI_WIDTH_AUTO
     */
    public static function setEmojiWidthMode(int $mode): void
    {
        self::$emojiWidthMode = $mode;
    }

    /**
     * Detect if terminal uses narrow emoji width (like PHPStorm).
     */
    private static function isNarrowEmojiTerminal(): bool
    {
        $term = getenv('TERM_PROGRAM') ?: getenv('TERM') ?: '';
        $termLower = strtolower($term);

        // PHPStorm and some IDEs use narrow emoji width
        return str_contains($termLower, 'phpstorm')
            || str_contains($termLower, 'intellij')
            || str_contains($termLower, 'jetbrains');
    }

    /**
     * Length of a string as it would be visibly rendered (ANSI ignored).
     * Uses mb_strwidth to account for wide characters (e.g., emojis, CJK).
     * Auto-detects terminal emoji width behavior or uses manual override.
     */
    public static function visibleLength(string $s): int
    {
        $noAnsi = self::stripAnsi($s);

        // Determine which width calculation to use
        $mode = self::$emojiWidthMode;

        if ($mode === self::EMOJI_WIDTH_AUTO) {
            // Auto-detect based on terminal
            $mode = self::isNarrowEmojiTerminal() ? self::EMOJI_WIDTH_NARROW : self::EMOJI_WIDTH_STANDARD;
        }

        if ($mode === self::EMOJI_WIDTH_STANDARD) {
            // Standard: emojis = 2 columns (WezTerm, Mac Terminal, most terminals)
            return mb_strwidth($noAnsi, 'UTF-8');
        } else {
            // Narrow: emojis = 1 column (PHPStorm, some IDEs)
            return mb_strlen($noAnsi, 'UTF-8');
        }
    }
}
