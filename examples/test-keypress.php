<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Ajaxray\AnsiKit\Support\Keypress;
use Ajaxray\AnsiKit\AnsiTerminal;

/**
 * Simple Test Runner for Keypress Component
 * 
 * This demonstrates how to test the Keypress component functionality
 * without requiring PHPUnit for basic validation.
 */

$terminal = new AnsiTerminal();

function testPassed(string $message): void
{
    global $terminal;
    $terminal->writeStyled("✓ PASS: $message", [AnsiTerminal::FG_GREEN])->newline();
}

function testFailed(string $message, string $expected, string $actual): void
{
    global $terminal;
    $terminal->writeStyled("✗ FAIL: $message", [AnsiTerminal::FG_RED])->newline();
    $terminal->writeStyled("  Expected: $expected", [AnsiTerminal::FG_YELLOW])->newline();
    $terminal->writeStyled("  Actual: $actual", [AnsiTerminal::FG_YELLOW])->newline();
}

function runTest(string $testName, callable $test): void
{
    global $terminal;
    $terminal->writeStyled("\nRunning: $testName", [AnsiTerminal::FG_CYAN])->newline();
    $terminal->write(str_repeat('-', 50))->newline();
    
    try {
        $test();
    } catch (Exception $e) {
        $terminal->writeStyled("✗ ERROR: " . $e->getMessage(), [AnsiTerminal::FG_RED])->newline();
    }
}

$terminal->writeStyled("🧪 Keypress Component Test Suite", [AnsiTerminal::TEXT_BOLD, AnsiTerminal::FG_CYAN])->newline();
$terminal->write(str_repeat('=', 50))->newline();

// Test 1: Constants are defined
runTest("Constants Definition Test", function() {
    $constants = [
        'KEY_UP' => 'UP',
        'KEY_DOWN' => 'DOWN', 
        'KEY_RIGHT' => 'RIGHT',
        'KEY_LEFT' => 'LEFT',
        'KEY_ENTER' => 'ENTER',
        'KEY_SPACE' => 'SPACE',
        'KEY_BACKSPACE' => 'BACKSPACE',
        'KEY_TAB' => 'TAB',
        'KEY_ESC' => 'ESC',
        'KEY_CTRL_A' => 'CTRL+A',
        'KEY_CTRL_C' => 'CTRL+C',
        'KEY_F1' => 'F1',
        'KEY_F12' => 'F12',
        'KEY_HOME' => 'HOME',
        'KEY_DELETE' => 'DELETE'
    ];
    
    foreach ($constants as $constant => $expectedValue) {
        if (!defined("Ajaxray\\AnsiKit\\Support\\Keypress::$constant")) {
            testFailed("Constant $constant not defined", "defined", "undefined");
            return;
        }
        
        $actualValue = constant("Ajaxray\\AnsiKit\\Support\\Keypress::$constant");
        if ($actualValue !== $expectedValue) {
            testFailed("Constant $constant has wrong value", $expectedValue, $actualValue);
            return;
        }
    }
    
    testPassed("All constants are properly defined with correct values");
});

// Test 2: translateKey method functionality
runTest("translateKey Method Test", function() {
    $reflection = new ReflectionClass(Keypress::class);
    $method = $reflection->getMethod('translateKey');
    $method->setAccessible(true);
    
    $testCases = [
        // Arrow keys
        ["\033[A", Keypress::KEY_UP, "Up arrow key"],
        ["\033[B", Keypress::KEY_DOWN, "Down arrow key"],
        ["\033[C", Keypress::KEY_RIGHT, "Right arrow key"],
        ["\033[D", Keypress::KEY_LEFT, "Left arrow key"],
        
        // Special keys
        ["\n", Keypress::KEY_ENTER, "Enter key"],
        [" ", Keypress::KEY_SPACE, "Space key"],
        ["\010", Keypress::KEY_BACKSPACE, "Backspace key (\\010)"],
        ["\177", Keypress::KEY_BACKSPACE, "Backspace key (\\177)"],
        ["\t", Keypress::KEY_TAB, "Tab key"],
        ["\e", Keypress::KEY_ESC, "Escape key"],
        
        // Ctrl keys
        ["\x01", Keypress::KEY_CTRL_A, "Ctrl+A"],
        ["\x03", Keypress::KEY_CTRL_C, "Ctrl+C"],
        ["\x1A", Keypress::KEY_CTRL_Z, "Ctrl+Z"],
        
        // Function keys
        ["\033OP", Keypress::KEY_F1, "F1 key"],
        ["\033[24~", Keypress::KEY_F12, "F12 key"],
        
        // Navigation keys
        ["\033[H", Keypress::KEY_HOME, "Home key"],
        ["\033[3~", Keypress::KEY_DELETE, "Delete key"],
        
        // Regular characters
        ["a", "a", "Regular character 'a'"],
        ["A", "A", "Regular character 'A'"],
        ["1", "1", "Regular character '1'"],
        ["!", "!", "Regular character '!'"],
        
        // Unknown sequences
        ["\033[Z", "\033[Z", "Unknown escape sequence"],
        ["", "", "Empty string"]
    ];
    
    foreach ($testCases as [$input, $expected, $description]) {
        $actual = $method->invoke(null, $input);
        if ($actual !== $expected) {
            testFailed($description, $expected, $actual);
            return;
        }
    }
    
    testPassed("All translateKey test cases passed");
});

// Test 3: getKeyName method
runTest("getKeyName Method Test", function() {
    $testCases = [
        [Keypress::KEY_UP, "UP ARROW"],
        [Keypress::KEY_CTRL_A, "CTRL+A"],
        [Keypress::KEY_F1, "F1"],
        [Keypress::KEY_HOME, "HOME"],
        ["a", "'a'"],
        ["\033[Z", "UNKNOWN SEQUENCE"]
    ];
    
    foreach ($testCases as [$input, $expected]) {
        $actual = Keypress::getKeyName($input);
        if ($actual !== $expected) {
            testFailed("getKeyName for '$input'", $expected, $actual);
            return;
        }
    }
    
    testPassed("All getKeyName test cases passed");
});

// Test 4: detectAltKey method
runTest("detectAltKey Method Test", function() {
    $testCases = [
        ["\033A", "ALT+A"],
        ["\0331", "ALT+1"],
        ["a", null],
        ["\033", null],
        ["\033[A", null]
    ];
    
    foreach ($testCases as [$input, $expected]) {
        $actual = Keypress::detectAltKey($input);
        if ($actual !== $expected) {
            $expectedStr = $expected ?? 'null';
            $actualStr = $actual ?? 'null';
            testFailed("detectAltKey for '$input'", $expectedStr, $actualStr);
            return;
        }
    }
    
    testPassed("All detectAltKey test cases passed");
});

// Test 5: Method signatures and accessibility
runTest("Method Signature Test", function() {
    $reflection = new ReflectionClass(Keypress::class);
    
    // Test listen method
    if (!$reflection->hasMethod('listen')) {
        testFailed("listen method exists", "exists", "missing");
        return;
    }
    
    $listenMethod = $reflection->getMethod('listen');
    if (!$listenMethod->isStatic()) {
        testFailed("listen method is static", "static", "non-static");
        return;
    }
    
    if (!$listenMethod->isPublic()) {
        testFailed("listen method is public", "public", "non-public");
        return;
    }
    
    // Test getKeyName method
    if (!$reflection->hasMethod('getKeyName')) {
        testFailed("getKeyName method exists", "exists", "missing");
        return;
    }
    
    $getKeyNameMethod = $reflection->getMethod('getKeyName');
    if (!$getKeyNameMethod->isStatic() || !$getKeyNameMethod->isPublic()) {
        testFailed("getKeyName method is public static", "public static", "incorrect signature");
        return;
    }
    
    // Test detectAltKey method
    if (!$reflection->hasMethod('detectAltKey')) {
        testFailed("detectAltKey method exists", "exists", "missing");
        return;
    }
    
    $detectAltKeyMethod = $reflection->getMethod('detectAltKey');
    if (!$detectAltKeyMethod->isStatic() || !$detectAltKeyMethod->isPublic()) {
        testFailed("detectAltKey method is public static", "public static", "incorrect signature");
        return;
    }
    
    testPassed("All method signatures are correct");
});

// Test 6: Integration test with match statement
runTest("Integration Test with Match Statement", function() {
    $reflection = new ReflectionClass(Keypress::class);
    $method = $reflection->getMethod('translateKey');
    $method->setAccessible(true);
    
    // Simulate a typical usage pattern
    $testInputs = ["\033[A", "\033[B", "\n", " ", "\e", "\x03"];
    
    foreach ($testInputs as $input) {
        $result = $method->invoke(null, $input);
        
        $action = match ($result) {
            Keypress::KEY_UP => 'move_up',
            Keypress::KEY_DOWN => 'move_down',
            Keypress::KEY_ENTER => 'select',
            Keypress::KEY_SPACE => 'action',
            Keypress::KEY_ESC => 'exit',
            Keypress::KEY_CTRL_C => 'interrupt',
            default => 'unknown'
        };
        
        if ($action === 'unknown') {
            testFailed("Match statement integration for input", "known action", "unknown");
            return;
        }
    }
    
    testPassed("Integration with match statements works correctly");
});

$terminal->newline();
$terminal->writeStyled("🎉 Test Suite Complete!", [AnsiTerminal::TEXT_BOLD, AnsiTerminal::FG_GREEN])->newline();
$terminal->writeStyled("Run the full PHPUnit test suite with: composer test", [AnsiTerminal::FG_CYAN])->newline();
$terminal->writeStyled("Or: ./vendor/bin/phpunit tests/Support/KeypressTest.php", [AnsiTerminal::FG_CYAN])->newline()->newline();
