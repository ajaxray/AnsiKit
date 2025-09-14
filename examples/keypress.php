<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Ajaxray\AnsiKit\Support\Keypress;
use Ajaxray\AnsiKit\AnsiTerminal;

/**
 * Keypress Component Examples
 * 
 * This file demonstrates various ways to use the Keypress component
 * for capturing and handling keyboard input in terminal applications.
 */

// Initialize AnsiTerminal for styling output
$terminal = new AnsiTerminal();

function showHeader(string $title): void
{
    global $terminal;
    $terminal->writeStyled($title, [AnsiTerminal::FG_CYAN, AnsiTerminal::TEXT_BOLD])->newline();
    $terminal->write(str_repeat('=', strlen($title)))->newline();
}

function showExample(string $description): void
{
    global $terminal;
    $terminal->writeStyled($description, [AnsiTerminal::FG_YELLOW])->newline();
}

function showInstruction(string $instruction): void
{
    global $terminal;
    $terminal->writeStyled($instruction, [AnsiTerminal::FG_GREEN])->newline();
}

function showResult(string $result): void
{
    global $terminal;
    $terminal->writeStyled("Result: $result", [AnsiTerminal::FG_MAGENTA])->newline();
}

// Example 1: Basic Key Detection
showHeader("Example 1: Basic Key Detection");
showExample("This example shows basic key detection and the use of constants.");
showInstruction("Press any key (try arrow keys, Enter, Esc, etc.). Press 'q' to continue to next example.");

while (true) {
    $key = Keypress::listen();
    
    $message = match ($key) {
        Keypress::KEY_UP => "Arrow Up pressed!",
        Keypress::KEY_DOWN => "Arrow Down pressed!",
        Keypress::KEY_LEFT => "Arrow Left pressed!",
        Keypress::KEY_RIGHT => "Arrow Right pressed!",
        Keypress::KEY_ENTER => "Enter key pressed!",
        Keypress::KEY_SPACE => "Space bar pressed!",
        Keypress::KEY_BACKSPACE => "Backspace pressed!",
        Keypress::KEY_TAB => "Tab key pressed!",
        Keypress::KEY_ESC => "Escape key pressed!",
        'q' => "Continuing to next example...",
        false => '',
        default => "Key pressed: '$key' (ASCII: " . ord($key) . ")"
    };
    
    showResult($message);
    
    if ($key === 'q') {
        break;
    }
}

// Example 2: Simple Menu Navigation
showHeader("Example 2: Simple Menu Navigation");
showExample("Navigate a simple menu using arrow keys.");

$menuItems = [
    "🏠 Home",
    "📁 Files", 
    "⚙️  Settings",
    "❓ Help",
    "🚪 Exit"
];

$selectedIndex = 0;

function displayMenu(array $items, int $selected): void
{
    global $terminal;
    
    // Clear screen and move cursor to top
    $terminal->clearScreen()->cursorHome();
    
    $terminal->writeStyled("📋 Main Menu", [AnsiTerminal::TEXT_BOLD, AnsiTerminal::FG_CYAN, AnsiTerminal::FG_CYAN])->newline();
    $terminal->writeStyled(str_repeat('─', 20))->newline();
    
    foreach ($items as $index => $item) {
        if ($index === $selected) {
            $terminal->writeStyled("► $item", [AnsiTerminal::FG_YELLOW, AnsiTerminal::BG_BLUE, AnsiTerminal::TEXT_BOLD])->newline();
        } else {
            $terminal->write("  $item")->newline();
        }
    }
    
    $terminal->writeStyled("Use ↑↓ arrows to navigate, Enter to select, Esc to continue", [AnsiTerminal::TEXT_DIM])->newline();
}

showInstruction("Use arrow keys to navigate, Enter to select, Esc to continue to next example.");

while (true) {
    displayMenu($menuItems, $selectedIndex);
    
    $key = Keypress::listen();
    
    switch ($key) {
        case Keypress::KEY_UP:
            $selectedIndex = ($selectedIndex - 1 + count($menuItems)) % count($menuItems);
            break;
            
        case Keypress::KEY_DOWN:
            $selectedIndex = ($selectedIndex + 1) % count($menuItems);
            break;
            
        case Keypress::KEY_ENTER:
            $terminal->writeStyled("Selected: " . $menuItems[$selectedIndex], [AnsiTerminal::FG_GREEN, AnsiTerminal::TEXT_BOLD])->newline();
            $terminal->writeStyled("Press any key to continue navigating or Esc to exit...", [AnsiTerminal::TEXT_DIM])->newline();

            $key = Keypress::listen();
            if ($key === Keypress::KEY_ESC) {
                break 2;
            }
            break;
            
        case Keypress::KEY_ESC:
            break 2;
    }
}

// Clear screen for next example
$terminal->clearScreen()->cursorHome();

// Example 3: Text Input with Special Key Handling
showHeader("Example 3: Text Input with Special Key Handling");
showExample("Build a simple text input that handles special keys.");
showInstruction("Type some text. Use Backspace to delete, Enter to submit, Esc to continue to next example.");

$inputText = "";

function displayTextInput(string $text): void
{
    global $terminal;

    $terminal->clearScreen()->cursorHome();
    $terminal->writeStyled("Input: ", [AnsiTerminal::FG_CYAN]);
    $terminal->writeStyled($text . "█", [AnsiTerminal::FG_YELLOW]);
}

while (true) {
    displayTextInput($inputText);
    
    $key = Keypress::listen();
    
    switch ($key) {
        case Keypress::KEY_BACKSPACE:
            if (strlen($inputText) > 0) {
                $inputText = substr($inputText, 0, -1);
            }
            break;
            
        case Keypress::KEY_ENTER:
            $terminal->writeStyled("You entered: '$inputText'", [AnsiTerminal::FG_GREEN])->newline();
            $terminal->writeStyled("Press any key to continue or Esc to exit...", [AnsiTerminal::TEXT_DIM])->newline();
            $key = Keypress::listen();
            if ($key === Keypress::KEY_ESC) {
                break 2;
            }
            $inputText = ""; // Reset input
            echo "\n";
            break;
            
        case Keypress::KEY_ESC:
            break 2;
            
        case Keypress::KEY_TAB:
        case Keypress::KEY_UP:
        case Keypress::KEY_DOWN:
        case Keypress::KEY_LEFT:
        case Keypress::KEY_RIGHT:
            // Ignore navigation keys in text input
            break;
            
        default:
            // Add printable characters to input
            if (strlen($key) === 1 && ord($key) >= 32 && ord($key) <= 126) {
                $inputText .= $key;
            }
            break;
    }
}

echo "\n";

// Example 4: Game-like Controls
showHeader("Example 4: Game-like Controls");
showExample("Demonstrate game-like controls with WASD and arrow keys.");
showInstruction("Use WASD or arrow keys to move the player (🎮). Press 'q' to quit.");

$playerX = 10;
$playerY = 5;
$boardWidth = 20;
$boardHeight = 10;

function displayGameBoard(int $x, int $y, int $width, int $height): void
{
    global $terminal;
    
    echo "\033[2J\033[H"; // Clear screen
    
    $terminal->writeStyled("🎮 Simple Game Controls", [AnsiTerminal::FG_CYAN, AnsiTerminal::TEXT_BOLD])->newline();
    $terminal->write(str_repeat('─', 30))->newline();
    
    for ($row = 0; $row < $height; $row++) {
        for ($col = 0; $col < $width; $col++) {
            if ($col === $x && $row === $y) {
                $terminal->writeStyled("🎮", [AnsiTerminal::FG_YELLOW]);
            } elseif ($row === 0 || $row === $height - 1 || $col === 0 || $col === $width - 1) {
                $terminal->writeStyled("█", [AnsiTerminal::FG_BLUE]);
            } else {
                echo " ";
            }
        }
        echo "\n";
    }
    
    $terminal->writeStyled("Position: ($x, $y)", [AnsiTerminal::FG_GREEN, AnsiTerminal::TEXT_BOLD])->newline()->newline();
    $terminal->writeStyled("Use WASD or arrow keys to move, 'q' to quit", [AnsiTerminal::TEXT_DIM])->newline();
}

while (true) {
    displayGameBoard($playerX, $playerY, $boardWidth, $boardHeight);
    
    $key = Keypress::listen();
    
    $newX = $playerX;
    $newY = $playerY;
    
    switch ($key) {
        case Keypress::KEY_UP:
        case 'w':
        case 'W':
            $newY = max(1, $playerY - 1);
            break;
            
        case Keypress::KEY_DOWN:
        case 's':
        case 'S':
            $newY = min($boardHeight - 2, $playerY + 1);
            break;
            
        case Keypress::KEY_LEFT:
        case 'a':
        case 'A':
            $newX = max(1, $playerX - 1);
            break;
            
        case Keypress::KEY_RIGHT:
        case 'd':
        case 'D':
            $newX = min($boardWidth - 2, $playerX + 1);
            break;
            
        case 'q':
        case 'Q':
        case Keypress::KEY_ESC:
            break 2;
    }
    
    $playerX = $newX;
    $playerY = $newY;
}

// Clear screen and show completion message
$terminal->clearScreen()->cursorHome();
showHeader("🎉 Keypress Examples Complete!");
$terminal->writeStyled("You've seen various ways to use the Keypress component:", [AnsiTerminal::FG_GREEN])->newline();
echo "• Basic key detection with constants\n";
echo "• Menu navigation\n";
echo "• Text input handling\n";
echo "• Game-like controls\n\n";
$terminal->writeStyled("The Keypress component provides a simple but powerful way to handle keyboard input in terminal applications!", [AnsiTerminal::FG_CYAN, AnsiTerminal::TEXT_BOLD])->newline();
