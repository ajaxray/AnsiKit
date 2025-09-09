<?php

require_once __DIR__ . '/vendor/autoload.php';

use Ajaxray\AnsiKit\AnsiTerminal;
use Ajaxray\AnsiKit\Components\Progressbar;
use Ajaxray\AnsiKit\Components\Spinner;

// Demonstrates re-rendering a block (status + progress bar) for ~5 seconds
$t = new AnsiTerminal();
$t->hideCursor()->clearScreen()->cursorHome();

$progress = new Progressbar();
$progress
    ->barStyle([AnsiTerminal::FG_GREEN])
    ->percentageStyle([AnsiTerminal::TEXT_BOLD])
    ->labelStyle([AnsiTerminal::FG_CYAN])
    ->borders('[', ']');

$totalSteps = 50;            // 50 steps x 100ms = ~5 seconds
$sleepMicros = 100_000;      // 100ms per step

// Default spinner is dotted; switch to ASCII with: new Spinner(Spinner::ASCII)
$spinner = new Spinner();
$phases  = ['Initializing', 'Connecting', 'Fetching', 'Processing', 'Finalizing'];

// Prime two lines to establish the render area
$t->write('Status: starting...')->newline();
$progress->renderLine(0, $totalSteps, 'Progress');

// Move back to the start of the 2-line block to begin re-rendering
$t->cursorUp(2);

for ($i = 0; $i <= $totalSteps; $i++) {
    $spin = $spinner->next();
    $phaseIndex = (int) floor(($i / max(1, $totalSteps)) * (count($phases)));
    $phaseIndex = min($phaseIndex, count($phases) - 1);
    $phase = $phases[$phaseIndex];

    // Build status text changing every step
    $status = sprintf('Status: %s %s (%d/%d)', $spin, $phase, $i, $totalSteps);

    // Redraw status line
    $t->clearLine()->writeStyled($status, [AnsiTerminal::FG_YELLOW])->newline();

    // Redraw progress bar line
    $t->clearLine();
    $progress->render($i, $totalSteps, 'Progress');
    $t->newline();

    // Move back to the start for the next iteration (except after final)
    if ($i < $totalSteps) {
        $t->cursorUp(2);
        usleep($sleepMicros);
    }
}

$t->newline()->writeStyled("✔ Done!\n", [AnsiTerminal::TEXT_BOLD, AnsiTerminal::FG_GREEN]);
$t->showCursor();
