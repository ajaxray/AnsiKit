<?php

require_once __DIR__ . '/vendor/autoload.php';

use Ajaxray\AnsiKit\AnsiTerminal;
use Ajaxray\AnsiKit\Components\Banner;
use Ajaxray\AnsiKit\Components\Table;
use Ajaxray\AnsiKit\Components\Progressbar;

// Terminal basics
$t = new AnsiTerminal();
$t->clearScreen()->cursorHome();

$t->writeStyled("Hello PHP Ninja! 🥷\n", [AnsiTerminal::TEXT_BOLD, AnsiTerminal::FG_GREEN]);
$t->fg256(202)->bg256(235)->write("256-colors")->reset()->newline();
$t->fgRGB(255, 165, 0)->write("truecolor")->reset()->newline();

// Table
$table = new Table();
$table->setHeaders('Table', 'Testing', 'Status')
    ->addRow('Application', 'Fancy Deployer', '✅')
    ->addRow('Version', 'v1.2.3', '❌')
    ->addRow('Environment', 'Staging', '⭕')
    ->render();

// Banner
$banner = new Banner();
$banner->render('Deploy Complete', '🚀', ['Everything shipped!', 'Tag: v1.2.3']);

$t->newline();

// Progress bars
$progressbar = new Progressbar();

// Basic progress bar
$progressbar->renderLine(75, 100, str_pad('Loading files...', 18));

// Styled progress bar, custom Border
$progressbar
    ->barStyle([AnsiTerminal::FG_GREEN])
    ->percentageStyle([AnsiTerminal::TEXT_BOLD])
    ->labelStyle([AnsiTerminal::FG_CYAN])
    ->borders('#', '#')
    ->renderLine(50, 100, str_pad('Processing data...', 18));

// Progress bar without percentage or count
$progressbar
    ->chars('█', '░')
    ->borders('(', ')')
    ->display(false, false)
    ->barStyle([AnsiTerminal::FG_RED])
    ->renderLine(25, 100, str_pad('No label task...', 18));

$t->newline();
$t->writeStyled("🎉 All examples complete!\n", [AnsiTerminal::TEXT_BOLD, AnsiTerminal::FG_GREEN]);

