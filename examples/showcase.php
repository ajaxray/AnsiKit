<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Ajaxray\AnsiKit\AnsiTerminal;
use Ajaxray\AnsiKit\Components\Banner;
use Ajaxray\AnsiKit\Components\Table;
use Ajaxray\AnsiKit\Components\Progressbar;

// Terminal basics
$t = new AnsiTerminal();
$t->clearScreen()->cursorHome();

$t->writeStyled("Hello PHP Ninja! 🥷\n", [AnsiTerminal::TEXT_BOLD, AnsiTerminal::FG_GREEN])->newline();

$t->writeStyled("Testing Colors... \n", [AnsiTerminal::TEXT_BOLD, AnsiTerminal::FG_CYAN]);
$t->fg256(202)->bg256(235)->write("Supports 256-colors")->reset()->newline();
$t->fgRGB(255, 165, 0)->write("Supports truecolor (RGB)")->reset()->newline();

// Table
$table = new Table();
$table->setHeaders('Table', 'Testing', 'Status')
    ->addRow('Application', 'Fancy Deployer', '✅')
    ->addRow('Version', 'v1.2.3', '❌')
    ->addRow('Environment', 'Staging', '⭕')
    ->render();


// Banner
(new Banner())->render('A Quick text banner');
$banner = new Banner();
$banner->render('Banner with details and style', ['Everything shipped!', 'Tag: v1.2.3'], 2, [AnsiTerminal::FG_GREEN, AnsiTerminal::TEXT_BOLD]);

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
