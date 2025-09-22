<?php

declare(strict_types=1);

namespace Ajaxray\AnsiKit\Support;

use Ajaxray\AnsiKit\AnsiTerminal;

/**
 * Simple, dependency-free input helpers for CLI interaction.
 */
final class Util
{
    public static function beep(?AnsiTerminal $terminal = null): void
    {
        $seq = "\007";

        if ($terminal) {
            $terminal->write($seq);
        } else {
            \file_put_contents('php://output', $seq);
        }
    }

    public static function setTerminalTabTitle(string $title, ?AnsiTerminal $terminal = null): void
    {
        $seq = AnsiTerminal::ESC . ']0;' . $title . "\007";
        if ($terminal) {
            $terminal->write($seq);
        } else {
            \file_put_contents('php://output', $seq);
        }
    }
}
