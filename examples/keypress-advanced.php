<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Ajaxray\AnsiKit\Support\Keypress;
use Ajaxray\AnsiKit\AnsiTerminal;

/**
 * Advanced Keypress Examples
 * 
 * This file demonstrates advanced keypress handling including:
 * - Modifier key detection (Ctrl, Alt, Shift combinations)
 * - Function key handling
 * - Raw key sequence inspection
 * - Terminal capability testing
 */

$terminal = new AnsiTerminal();

function showHeader(string $title): void
{
    global $terminal;
    $terminal->newline();
    $terminal->writeStyled($title, [AnsiTerminal::FG_CYAN, AnsiTerminal::TEXT_BOLD])->newline();
    $terminal->write(str_repeat('=', strlen($title)))->newline()->newline();
}

function showKeyInfo(string $key): void
{
    global $terminal;

    $keyName = Keypress::getKeyName($key);
    $ascii = '';
    $hex = '';

    // Show ASCII values for each character
    for ($i = 0; $i < strlen($key); $i++) {
        $char = $key[$i];
        $ascii .= ord($char) . ' ';
        $hex .= sprintf('%02X ', ord($char));
    }

    $terminal->writeStyled("Key: ", [AnsiTerminal::FG_YELLOW]);
    $terminal->writeStyled($keyName, [AnsiTerminal::TEXT_BOLD, AnsiTerminal::FG_WHITE])->newline();
    $terminal->writeStyled("Raw: ", [AnsiTerminal::FG_CYAN]);
    $terminal->writeStyled("'$key'", [AnsiTerminal::FG_WHITE])->newline();
    $terminal->writeStyled("ASCII: ", [AnsiTerminal::FG_GREEN]);
    $terminal->writeStyled(trim($ascii), [AnsiTerminal::FG_WHITE])->newline();
    $terminal->writeStyled("Hex: ", [AnsiTerminal::FG_MAGENTA]);
    $terminal->writeStyled(trim($hex), [AnsiTerminal::FG_WHITE])->newline();
    $terminal->write(str_repeat('-', 40))->newline();
}



// Example 1: Key Inspector
showHeader("Example 1: Key Inspector");
$terminal->writeStyled("Press any key to see detailed information about it.", [AnsiTerminal::FG_GREEN])->newline();
$terminal->writeStyled("Try: Arrow keys, Ctrl+letters, Alt+letters, Function keys, etc.", [AnsiTerminal::FG_YELLOW])->newline();
$terminal->writeStyled("Press 'q' to continue to next example.", [AnsiTerminal::TEXT_DIM])->newline()->newline();

while (true) {
    $key = Keypress::listen();
    
    if ($key === 'q') {
        $terminal->writeStyled("Continuing to next example...", [AnsiTerminal::FG_GREEN])->newline();
        break;
    }

    // Check for Alt key combinations
    $altKey = Keypress::detectAltKey($key);
    if ($altKey) {
        $terminal->writeStyled("Detected: $altKey", [AnsiTerminal::TEXT_BOLD, AnsiTerminal::FG_YELLOW])->newline();
        $terminal->writeStyled("Raw sequence: ", [AnsiTerminal::FG_CYAN]);
        for ($i = 0; $i < strlen($key); $i++) {
            printf("\\x%02X", ord($key[$i]));
        }
        $terminal->newline();
        $terminal->write(str_repeat('-', 40))->newline();
    } else {
        showKeyInfo($key);
    }
}

// Example 2: Modifier Key Shortcuts
showHeader("Example 2: Common Keyboard Shortcuts");
$terminal->writeStyled("Try common keyboard shortcuts:", [AnsiTerminal::FG_GREEN])->newline();
$terminal->writeStyled("• Ctrl+C (Copy/Interrupt)", [AnsiTerminal::FG_WHITE])->newline();
$terminal->writeStyled("• Ctrl+V (Paste)", [AnsiTerminal::FG_WHITE])->newline();
$terminal->writeStyled("• Ctrl+Z (Undo/Suspend)", [AnsiTerminal::FG_WHITE])->newline();
$terminal->writeStyled("• Ctrl+S (Save)", [AnsiTerminal::FG_WHITE])->newline();
$terminal->writeStyled("• Ctrl+Q (Quit)", [AnsiTerminal::FG_WHITE])->newline();
$terminal->writeStyled("• Alt+F4 (Close)", [AnsiTerminal::FG_WHITE])->newline();
$terminal->writeStyled("Press 'q' to continue.", [AnsiTerminal::TEXT_DIM])->newline()->newline();

$shortcuts = [
    Keypress::KEY_CTRL_C => "Ctrl+C - Interrupt/Copy",
    Keypress::KEY_CTRL_V => "Ctrl+V - Paste",
    Keypress::KEY_CTRL_Z => "Ctrl+Z - Undo/Suspend",
    Keypress::KEY_CTRL_S => "Ctrl+S - Save",
    Keypress::KEY_CTRL_Q => "Ctrl+Q - Quit",
    Keypress::KEY_CTRL_A => "Ctrl+A - Select All",
    Keypress::KEY_CTRL_F => "Ctrl+F - Find",
    Keypress::KEY_CTRL_N => "Ctrl+N - New",
    Keypress::KEY_CTRL_O => "Ctrl+O - Open",
];

while (true) {
    $key = Keypress::listen();

    if ($key === 'q') {
        $terminal->writeStyled("Continuing to next example...", [AnsiTerminal::FG_GREEN])->newline();
        break;
    }

    if (isset($shortcuts[$key])) {
        $terminal->writeStyled("✓ Recognized: " . $shortcuts[$key], [AnsiTerminal::TEXT_BOLD, AnsiTerminal::FG_GREEN])->newline();
    } else {
        $terminal->writeStyled("Key pressed: " . Keypress::getKeyName($key), [AnsiTerminal::FG_YELLOW])->newline();
    }
}

// Example 3: Terminal Capability Testing
showHeader("Example 3: Terminal Capability Testing");
$terminal->writeStyled("Testing what your terminal supports...", [AnsiTerminal::FG_GREEN])->newline()->newline();

$terminal->writeStyled("Testing basic keys:", [AnsiTerminal::FG_CYAN])->newline();
$terminal->write("✓ Arrow keys: Supported")->newline();
$terminal->write("✓ Function keys: Try F1-F12")->newline();
$terminal->write("✓ Modifier combinations: Try Ctrl+Arrow, Alt+Arrow, Shift+Arrow")->newline()->newline();

$terminal->writeStyled("Press different key combinations to test support:", [AnsiTerminal::FG_YELLOW])->newline();
$terminal->writeStyled("Press 'q' to finish.", [AnsiTerminal::TEXT_DIM])->newline()->newline();

$testedKeys = [];

while (true) {
    $key = Keypress::listen();
    
    if ($key === 'q') {
        break;
    }
    
    $keyName = Keypress::getKeyName($key);
    
    if (!in_array($keyName, $testedKeys)) {
        $testedKeys[] = $keyName;
        
        $support = match (true) {
            str_contains($keyName, 'CTRL+') => "🟢 Ctrl modifier supported",
            str_contains($keyName, 'ALT+') => "🟢 Alt modifier supported", 
            str_contains($keyName, 'SHIFT+') => "🟢 Shift modifier supported",
            str_contains($keyName, 'F') && is_numeric(substr($keyName, 1)) => "🟢 Function key supported",
            $keyName === 'UNKNOWN SEQUENCE' => "🔴 Unknown/unsupported sequence",
            default => "🟡 Basic key"
        };
        
        $terminal->writeStyled("$keyName: ", [AnsiTerminal::FG_WHITE]);
        $terminal->write($support)->newline();
    }
}

// Summary
$terminal->newline();
$terminal->writeStyled("🎉 Advanced Keypress Testing Complete!", [AnsiTerminal::TEXT_BOLD, AnsiTerminal::FG_GREEN])->newline()->newline();
$terminal->writeStyled("Summary of tested keys:", [AnsiTerminal::FG_CYAN])->newline();
foreach ($testedKeys as $key) {
    $terminal->write("• $key")->newline();
}

$terminal->newline();
$terminal->writeStyled("Note:", [AnsiTerminal::FG_YELLOW]);
$terminal->write(" Terminal support varies by emulator and OS.")->newline();
$terminal->writeStyled("Some key combinations may be intercepted by the system or terminal.", [AnsiTerminal::TEXT_DIM])->newline()->newline();
