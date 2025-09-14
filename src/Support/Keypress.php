<?php

declare(strict_types=1);

namespace Ajaxray\AnsiKit\Support;

/**
 * Keypress Listener
 */
final class Keypress
{
    // Basic key constants
    public const KEY_UP = 'UP';
    public const KEY_DOWN = 'DOWN';
    public const KEY_RIGHT = 'RIGHT';
    public const KEY_LEFT = 'LEFT';
    public const KEY_ENTER = 'ENTER';
    public const KEY_SPACE = 'SPACE';
    public const KEY_BACKSPACE = 'BACKSPACE';
    public const KEY_TAB = 'TAB';
    public const KEY_ESC = 'ESC';

    // Ctrl key constants
    public const KEY_CTRL_A = 'CTRL+A';
    public const KEY_CTRL_B = 'CTRL+B';
    public const KEY_CTRL_C = 'CTRL+C';
    public const KEY_CTRL_D = 'CTRL+D';
    public const KEY_CTRL_E = 'CTRL+E';
    public const KEY_CTRL_F = 'CTRL+F';
    public const KEY_CTRL_G = 'CTRL+G';
    public const KEY_CTRL_H = 'CTRL+H';
    public const KEY_CTRL_K = 'CTRL+K';
    public const KEY_CTRL_L = 'CTRL+L';
    public const KEY_CTRL_N = 'CTRL+N';
    public const KEY_CTRL_O = 'CTRL+O';
    public const KEY_CTRL_P = 'CTRL+P';
    public const KEY_CTRL_Q = 'CTRL+Q';
    public const KEY_CTRL_R = 'CTRL+R';
    public const KEY_CTRL_S = 'CTRL+S';
    public const KEY_CTRL_T = 'CTRL+T';
    public const KEY_CTRL_U = 'CTRL+U';
    public const KEY_CTRL_V = 'CTRL+V';
    public const KEY_CTRL_W = 'CTRL+W';
    public const KEY_CTRL_X = 'CTRL+X';
    public const KEY_CTRL_Y = 'CTRL+Y';
    public const KEY_CTRL_Z = 'CTRL+Z';

    // Function key constants
    public const KEY_F1 = 'F1';
    public const KEY_F2 = 'F2';
    public const KEY_F3 = 'F3';
    public const KEY_F4 = 'F4';
    public const KEY_F5 = 'F5';
    public const KEY_F6 = 'F6';
    public const KEY_F7 = 'F7';
    public const KEY_F8 = 'F8';
    public const KEY_F9 = 'F9';
    public const KEY_F10 = 'F10';
    public const KEY_F11 = 'F11';
    public const KEY_F12 = 'F12';

    // Modified arrow key constants
    public const KEY_CTRL_UP = 'CTRL+UP';
    public const KEY_CTRL_DOWN = 'CTRL+DOWN';
    public const KEY_CTRL_RIGHT = 'CTRL+RIGHT';
    public const KEY_CTRL_LEFT = 'CTRL+LEFT';
    public const KEY_ALT_UP = 'ALT+UP';
    public const KEY_ALT_DOWN = 'ALT+DOWN';
    public const KEY_ALT_RIGHT = 'ALT+RIGHT';
    public const KEY_ALT_LEFT = 'ALT+LEFT';
    public const KEY_SHIFT_UP = 'SHIFT+UP';
    public const KEY_SHIFT_DOWN = 'SHIFT+DOWN';
    public const KEY_SHIFT_RIGHT = 'SHIFT+RIGHT';
    public const KEY_SHIFT_LEFT = 'SHIFT+LEFT';

    // Navigation key constants
    public const KEY_HOME = 'HOME';
    public const KEY_END = 'END';
    public const KEY_PAGE_UP = 'PAGE UP';
    public const KEY_PAGE_DOWN = 'PAGE DOWN';
    public const KEY_INSERT = 'INSERT';
    public const KEY_DELETE = 'DELETE';
    /**
     * Listen for keypress and return the pressed key.
     */
    public static function listen(): string
    {
        $stdin = fopen('php://stdin', 'r');

        // changing your terminal mode so it
        // - doesn’t echo characters
        // - reads input in cbreak mode (i.e. characters are available immediately)
        // - make the stream non-blocking (so reads return immediately if there’s no data)
        stream_set_blocking($stdin, false);
        system('stty cbreak -echo');

        // Read a single character from STDIN
        while (true) {
            $char = fgets($stdin);
            if ($char !== false) {
                break;
            }
        }

        // restore the terminal mode
        system('stty sane');
        stream_set_blocking($stdin, true);

        fclose($stdin);

        return self::translateKey($char);
    }

    private static function translateKey(string $key): string
    {
        return match ($key) {
            // Basic arrow keys
            "\033[A" => self::KEY_UP,
            "\033[B" => self::KEY_DOWN,
            "\033[C" => self::KEY_RIGHT,
            "\033[D" => self::KEY_LEFT,

            // Basic special keys
            "\n" => self::KEY_ENTER,
            " " => self::KEY_SPACE,
            "\010", "\177" => self::KEY_BACKSPACE,
            "\t" => self::KEY_TAB,
            "\e" => self::KEY_ESC,

            // Ctrl combinations
            "\x01" => self::KEY_CTRL_A,
            "\x02" => self::KEY_CTRL_B,
            "\x03" => self::KEY_CTRL_C,
            "\x04" => self::KEY_CTRL_D,
            "\x05" => self::KEY_CTRL_E,
            "\x06" => self::KEY_CTRL_F,
            "\x07" => self::KEY_CTRL_G,
            "\x08" => self::KEY_CTRL_H,
            "\x0B" => self::KEY_CTRL_K,
            "\x0C" => self::KEY_CTRL_L,
            "\x0E" => self::KEY_CTRL_N,
            "\x0F" => self::KEY_CTRL_O,
            "\x10" => self::KEY_CTRL_P,
            "\x11" => self::KEY_CTRL_Q,
            "\x12" => self::KEY_CTRL_R,
            "\x13" => self::KEY_CTRL_S,
            "\x14" => self::KEY_CTRL_T,
            "\x15" => self::KEY_CTRL_U,
            "\x16" => self::KEY_CTRL_V,
            "\x17" => self::KEY_CTRL_W,
            "\x18" => self::KEY_CTRL_X,
            "\x19" => self::KEY_CTRL_Y,
            "\x1A" => self::KEY_CTRL_Z,

            // Function keys
            "\033OP" => self::KEY_F1,
            "\033OQ" => self::KEY_F2,
            "\033OR" => self::KEY_F3,
            "\033OS" => self::KEY_F4,
            "\033[15~" => self::KEY_F5,
            "\033[17~" => self::KEY_F6,
            "\033[18~" => self::KEY_F7,
            "\033[19~" => self::KEY_F8,
            "\033[20~" => self::KEY_F9,
            "\033[21~" => self::KEY_F10,
            "\033[23~" => self::KEY_F11,
            "\033[24~" => self::KEY_F12,

            // Modified arrow keys (Ctrl+Arrow)
            "\033[1;5A" => self::KEY_CTRL_UP,
            "\033[1;5B" => self::KEY_CTRL_DOWN,
            "\033[1;5C" => self::KEY_CTRL_RIGHT,
            "\033[1;5D" => self::KEY_CTRL_LEFT,

            // Modified arrow keys (Alt+Arrow)
            "\033[1;3A" => self::KEY_ALT_UP,
            "\033[1;3B" => self::KEY_ALT_DOWN,
            "\033[1;3C" => self::KEY_ALT_RIGHT,
            "\033[1;3D" => self::KEY_ALT_LEFT,

            // Modified arrow keys (Shift+Arrow)
            "\033[1;2A" => self::KEY_SHIFT_UP,
            "\033[1;2B" => self::KEY_SHIFT_DOWN,
            "\033[1;2C" => self::KEY_SHIFT_RIGHT,
            "\033[1;2D" => self::KEY_SHIFT_LEFT,

            // Navigation keys
            "\033[H" => self::KEY_HOME,
            "\033[F" => self::KEY_END,
            "\033[5~" => self::KEY_PAGE_UP,
            "\033[6~" => self::KEY_PAGE_DOWN,
            "\033[2~" => self::KEY_INSERT,
            "\033[3~" => self::KEY_DELETE,

            default => $key,
        };
    }

    /**
     * Get a human-readable name for a key sequence.
     */
    public static function getKeyName(string $key): string
    {
        return match ($key) {
            self::KEY_UP => "UP ARROW",
            self::KEY_DOWN => "DOWN ARROW",
            self::KEY_LEFT => "LEFT ARROW",
            self::KEY_RIGHT => "RIGHT ARROW",
            self::KEY_ENTER => "ENTER",
            self::KEY_SPACE => "SPACE",
            self::KEY_BACKSPACE => "BACKSPACE",
            self::KEY_TAB => "TAB",
            self::KEY_ESC => "ESCAPE",

            // Ctrl combinations
            self::KEY_CTRL_A => "CTRL+A",
            self::KEY_CTRL_B => "CTRL+B",
            self::KEY_CTRL_C => "CTRL+C",
            self::KEY_CTRL_D => "CTRL+D",
            self::KEY_CTRL_E => "CTRL+E",
            self::KEY_CTRL_F => "CTRL+F",
            self::KEY_CTRL_G => "CTRL+G",
            self::KEY_CTRL_H => "CTRL+H",
            self::KEY_CTRL_K => "CTRL+K",
            self::KEY_CTRL_L => "CTRL+L",
            self::KEY_CTRL_N => "CTRL+N",
            self::KEY_CTRL_O => "CTRL+O",
            self::KEY_CTRL_P => "CTRL+P",
            self::KEY_CTRL_Q => "CTRL+Q",
            self::KEY_CTRL_R => "CTRL+R",
            self::KEY_CTRL_S => "CTRL+S",
            self::KEY_CTRL_T => "CTRL+T",
            self::KEY_CTRL_U => "CTRL+U",
            self::KEY_CTRL_V => "CTRL+V",
            self::KEY_CTRL_W => "CTRL+W",
            self::KEY_CTRL_X => "CTRL+X",
            self::KEY_CTRL_Y => "CTRL+Y",
            self::KEY_CTRL_Z => "CTRL+Z",

            // Function keys
            self::KEY_F1 => "F1",
            self::KEY_F2 => "F2",
            self::KEY_F3 => "F3",
            self::KEY_F4 => "F4",
            self::KEY_F5 => "F5",
            self::KEY_F6 => "F6",
            self::KEY_F7 => "F7",
            self::KEY_F8 => "F8",
            self::KEY_F9 => "F9",
            self::KEY_F10 => "F10",
            self::KEY_F11 => "F11",
            self::KEY_F12 => "F12",

            // Modified arrow keys
            self::KEY_CTRL_UP => "CTRL+UP",
            self::KEY_CTRL_DOWN => "CTRL+DOWN",
            self::KEY_CTRL_RIGHT => "CTRL+RIGHT",
            self::KEY_CTRL_LEFT => "CTRL+LEFT",
            self::KEY_ALT_UP => "ALT+UP",
            self::KEY_ALT_DOWN => "ALT+DOWN",
            self::KEY_ALT_RIGHT => "ALT+RIGHT",
            self::KEY_ALT_LEFT => "ALT+LEFT",
            self::KEY_SHIFT_UP => "SHIFT+UP",
            self::KEY_SHIFT_DOWN => "SHIFT+DOWN",
            self::KEY_SHIFT_RIGHT => "SHIFT+RIGHT",
            self::KEY_SHIFT_LEFT => "SHIFT+LEFT",

            // Navigation keys
            self::KEY_HOME => "HOME",
            self::KEY_END => "END",
            self::KEY_PAGE_UP => "PAGE UP",
            self::KEY_PAGE_DOWN => "PAGE DOWN",
            self::KEY_INSERT => "INSERT",
            self::KEY_DELETE => "DELETE",

            default => strlen($key) === 1 && ord($key) >= 32 && ord($key) <= 126
                ? "'" . $key . "'"
                : "UNKNOWN SEQUENCE"
        };
    }

    /**
     * Detect Alt key combinations (ESC prefix).
     */
    public static function detectAltKey(string $key): ?string
    {
        // Alt keys are often prefixed with ESC
        if (strlen($key) >= 2 && $key[0] === "\033") {
            $char = substr($key, 1);
            if (strlen($char) === 1 && ord($char) >= 32 && ord($char) <= 126) {
                return "ALT+" . strtoupper($char);
            }
        }
        return null;
    }
}

