<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Ajaxray\AnsiKit\AnsiTerminal;
use Ajaxray\AnsiKit\Components\Choice;

$t = new AnsiTerminal();
$t->clearScreen()->cursorHome();

$t->writeStyled("🎯 AnsiKit Choice Component Demo\n", [AnsiTerminal::TEXT_BOLD, AnsiTerminal::FG_CYAN]);
$t->newline();

// Example 1: Basic required choice
$t->writeStyled("Example 1: Basic Required Choice\n", [AnsiTerminal::TEXT_BOLD, AnsiTerminal::FG_YELLOW]);
$t->write("This choice is required - you must select one of the options.\n");
$t->newline();

$choice = new Choice();
$selected = $choice->prompt('Choose your favorite programming language:', [
    'PHP',
    'JavaScript',
    'Python',
    'Go',
    'Rust'
]);

$t->newline();
$t->writeStyled("✅ You selected: {$selected}\n", [AnsiTerminal::FG_GREEN]);
$t->newline(2);

// Example 2: Optional choice with Exit option
$t->writeStyled("Example 2: Optional Choice (with Exit option)\n", [AnsiTerminal::TEXT_BOLD, AnsiTerminal::FG_YELLOW]);
$t->write("This choice is optional - you can select 'Exit' to skip.\n");
$t->newline();

$choice = new Choice();
$selected = $choice
    ->required(false)
    ->prompt('Choose a deployment action:', [
        'Deploy to Production',
        'Deploy to Staging',
        'Run Tests',
        'View Logs',
        'Rollback'
    ]);

$t->newline();
if ($selected === false) {
    $t->writeStyled("❌ You chose to exit without selecting an action.\n", [AnsiTerminal::FG_RED]);
} else {
    $t->writeStyled("✅ You selected: {$selected}\n", [AnsiTerminal::FG_GREEN]);
}
$t->newline(2);

// Example 3: Styled choice
$t->writeStyled("Example 3: Styled Choice\n", [AnsiTerminal::TEXT_BOLD, AnsiTerminal::FG_YELLOW]);
$t->write("This choice demonstrates custom styling options.\n");
$t->newline();

$choice = new Choice();
$selected = $choice
    ->required(false)
    ->promptStyle([AnsiTerminal::TEXT_BOLD, AnsiTerminal::FG_MAGENTA])
    ->optionStyle([AnsiTerminal::FG_CYAN])
    ->numberStyle([AnsiTerminal::TEXT_BOLD, AnsiTerminal::FG_YELLOW])
    ->errorStyle([AnsiTerminal::TEXT_BOLD, AnsiTerminal::FG_RED])
    ->exitStyle([AnsiTerminal::FG_BRIGHT_BLACK])
    ->prompt('🎨 Choose a color theme:', [
        'Dark Mode',
        'Light Mode',
        'High Contrast',
        'Custom'
    ]);

$t->newline();
if ($selected === false) {
    $t->writeStyled("❌ No theme selected.\n", [AnsiTerminal::FG_RED]);
} else {
    $t->writeStyled("✅ Theme selected: {$selected}\n", [AnsiTerminal::FG_GREEN]);
}
$t->newline(2);

// Example 4: Simple yes/no choice
$t->writeStyled("Example 4: Simple Yes/No Choice\n", [AnsiTerminal::TEXT_BOLD, AnsiTerminal::FG_YELLOW]);
$t->write("A simple binary choice example.\n");
$t->newline();

$choice = new Choice();
$selected = $choice->prompt('Do you want to continue?', ['Yes', 'No']);

$t->newline();
if ($selected === 'Yes') {
    $t->writeStyled("✅ Continuing...\n", [AnsiTerminal::FG_GREEN]);
} else {
    $t->writeStyled("❌ Operation cancelled.\n", [AnsiTerminal::FG_RED]);
}

$t->newline();
$t->writeStyled("🎉 Choice component demo complete!\n", [AnsiTerminal::TEXT_BOLD, AnsiTerminal::FG_GREEN]);
