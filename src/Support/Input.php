<?php

declare(strict_types=1);

namespace Ajaxray\AnsiKit\Support;

/**
 * Simple, dependency-free input helpers for CLI interaction.
 */
final class Input
{
    /**
     * Read a single line from STDIN after showing an optional prompt.
     * If the user submits an empty line and a default is provided, default is returned.
     */
    public static function line(string $prompt = '', ?string $default = null): string
    {
        // Prefer readline() if available for better UX (line editing, history)
        if (\function_exists('readline')) {
            $input = \readline($prompt);
            if ($input === false) {
                return $default ?? '';
            }
            $val = $input; // readline does not include trailing newline
            if ($val === '' && $default !== null) {
                return $default;
            }
            if ($val !== '' && \function_exists('readline_add_history')) {
                \readline_add_history($val);
            }
            return $val;
        }

        // Fallback to basic STDIN read
        if ($prompt !== '') {
            self::write($prompt);
        }
        $raw = self::read();
        if ($raw === false) {
            return $default ?? '';
        }
        $val = rtrim($raw, "\r\n");
        if ($val === '' && $default !== null) {
            return $default;
        }
        return $val;
    }

    /**
     * Read multiple lines from STDIN.
     * - If $terminator is null, an empty line ends input.
     * - If $terminator is provided, a line equal to that string ends input (not included in result).
     * Returns the collected lines joined by PHP_EOL.
     */
    public static function multiline(string $prompt = '', ?string $terminator = null): string
    {
        if ($prompt !== '') {
            if ($terminator === null) {
                self::write($prompt . "\n(Submit an empty line to finish)\n");
            } else {
                self::write($prompt . "\n(End with a line containing: {$terminator})\n");
            }
        }

        $lines = [];
        while (true) {
            $raw = self::read();
            if ($raw === false) {
                break; // EOF
            }
            $line = rtrim($raw, "\r\n");

            if ($terminator === null) {
                if ($line === '') {
                    break; // empty line ends input
                }
            } else {
                if ($line === $terminator) {
                    break; // explicit terminator
                }
            }

            $lines[] = $line;
        }

        return implode(PHP_EOL, $lines);
    }

    /**
     * Ask for a confirmation (yes/no). Keeps prompting until recognized input is entered.
     * Accepts: y/yes/true/1 and n/no/false/0 (case-insensitive). Empty input returns $default.
     */
    public static function confirm(string $question, bool $default = true): bool
    {
        $suffix = $default ? ' [Y/n]: ' : ' [y/N]: ';

        while (true) {
            self::write($question . $suffix);
            $raw = self::read();
            if ($raw === false) {
                return $default; // non-interactive / EOF
            }
            $ans = strtolower(trim($raw));
            if ($ans === '') {
                return $default;
            }

            if (in_array($ans, ['y', 'yes', 'true', '1', 'on'], true)) {
                return true;
            }
            if (in_array($ans, ['n', 'no', 'false', '0', 'off'], true)) {
                return false;
            }

            // Invalid input, show a gentle hint and re-prompt
            self::write("Please answer 'y' or 'n'.\n");
        }
    }

    private static function read(): string|false
    {
        if (\defined('STDIN')) {
            return \fgets(STDIN);
        }
        $h = @\fopen('php://stdin', 'r');
        if ($h === false) {
            return false;
        }
        $line = \fgets($h);
        @\fclose($h);
        return $line;
    }

    private static function write(string $s): void
    {
        if (\defined('STDOUT')) {
            \fwrite(STDOUT, $s);
            return;
        }
        echo $s;
    }
}
