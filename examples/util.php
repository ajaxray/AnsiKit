<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Ajaxray\AnsiKit\AnsiTerminal;
use Ajaxray\AnsiKit\Support\Util;

$t = new AnsiTerminal();
$t->clearScreen()->cursorHome();

$t->writeStyled("🔧 Util helper demo\n", [AnsiTerminal::TEXT_BOLD, AnsiTerminal::FG_CYAN]);
$t->newline();

$title = 'AnsiKit Util Demo';
$t->write("Setting terminal/tab title to '{$title}'...\n");
Util::setTerminalTabTitle($title, $t);

$t->write("Triggering an audible/visual bell via injected terminal in 1 second...\n");
sleep(1);
Util::beep($t);

$t->newline();
$t->write("Triggering fallback bell using STDOUT directly...\n");
sleep(1);
Util::beep();

$t->newline();
$t->write("Press Enter to restore your previous tab title and exit.\n");
fgets(STDIN);

// Restoring the title of terminal tab
Util::restoreTerminalTabTitle($t);
$t->writeStyled("Tab title reset. Bye!\n", [AnsiTerminal::FG_GREEN]);
