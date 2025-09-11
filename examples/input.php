<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Ajaxray\AnsiKit\AnsiTerminal;
use Ajaxray\AnsiKit\Support\Input;

$t = new AnsiTerminal();
$t->clearScreen()->cursorHome();

// Single-line input
$name = Input::line('What is your name? [Anonymous] ', 'Anonymous');
$t->writeStyled("Hello, {$name}!\n", [AnsiTerminal::TEXT_BOLD, AnsiTerminal::FG_GREEN]);

// Confirmation
$proceed = Input::confirm('Do you want to enter a short bio?', true);
if (!$proceed) {
    $t->writeStyled("Okay, skipping bio.\n", [AnsiTerminal::FG_YELLOW]);
    exit(0);
}

// Multi-line input with explicit terminator
$bio = Input::multiline("Enter your bio below. End with a line containing 'END'", 'END');

$t->newline();
$t->writeStyled("Your bio:\n", [AnsiTerminal::TEXT_BOLD]);
$t->write($bio . "\n");

$t->writeStyled("Thanks!\n", [AnsiTerminal::FG_CYAN]);

