<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Ajaxray\AnsiKit\AnsiTerminal;
use Ajaxray\AnsiKit\Components\Panel;
use Ajaxray\AnsiKit\Components\PanelBlock;
use Ajaxray\AnsiKit\Components\Table;
use Ajaxray\AnsiKit\Components\Banner;

$t = new AnsiTerminal();
$t->clearScreen()->cursorHome();

$t->writeStyled("Panel Component Examples\n", [AnsiTerminal::TEXT_BOLD, AnsiTerminal::FG_CYAN]);
$t->newline();

// Example 1: Simple vertical layout
$t->writeStyled("1. Vertical Layout (Stacked Rows)\n", [AnsiTerminal::TEXT_BOLD]);

$panel1 = new Panel();
$block1 = (new PanelBlock())->content('First Block')->width(40);
$block2 = (new PanelBlock())->content('Second Block')->width(40);
$block3 = (new PanelBlock())->content('Third Block')->width(40);

$panel1->layout('vertical')
    ->border(true)
    ->dividers(true)
    ->addBlock($block1)
    ->addBlock($block2)
    ->addBlock($block3)
    ->render();

$t->newline();

// Example 2: Horizontal layout
$t->writeStyled("2. Horizontal Layout (Side-by-Side Columns)\n", [AnsiTerminal::TEXT_BOLD]);

$panel2 = new Panel();
$leftBlock = (new PanelBlock())->content("Left Column\nLine 2\nLine 3")->width(20);
$middleBlock = (new PanelBlock())->content("Middle Column\nMore text\nEven more")->width(20);
$rightBlock = (new PanelBlock())->content("Right Column\nData here\nLast line")->width(20);

$panel2->layout('horizontal')
    ->border(true)
    ->dividers(true)
    ->addBlock($leftBlock)
    ->addBlock($middleBlock)
    ->addBlock($rightBlock)
    ->render();

$t->newline();

// Example 3: Blocks with individual borders
$t->writeStyled("3. Blocks with Individual Borders\n", [AnsiTerminal::TEXT_BOLD]);

$panel3 = new Panel();
$borderedBlock1 = (new PanelBlock())->content('Bordered Block 1')->width(25)->border(true);
$borderedBlock2 = (new PanelBlock())->content('Bordered Block 2')->width(25)->border(true);

$panel3->layout('vertical')
    ->addBlock($borderedBlock1)
    ->addBlock($borderedBlock2)
    ->render();

$t->newline();

// Example 4: Word wrapping
$t->writeStyled("4. Word Wrapping in Blocks\n", [AnsiTerminal::TEXT_BOLD]);

$panel4 = new Panel();
$longText = "This is a very long text that will be wrapped automatically when it exceeds the specified width of the block. The word wrap feature ensures content fits nicely.";
$wrappedBlock = (new PanelBlock())
    ->content($longText)
    ->width(40)
    ->overflow('wordwrap')
    ->border(true);

$panel4->addBlock($wrappedBlock)->render();

$t->newline();

// Example 5: Custom sizes for horizontal layout
$t->writeStyled("5. Custom Column Sizes\n", [AnsiTerminal::TEXT_BOLD]);

$panel5 = new Panel();
$smallBlock = (new PanelBlock())->content("Small\n10 cols")->width(10);
$mediumBlock = (new PanelBlock())->content("Medium\n20 cols")->width(20);
$largeBlock = (new PanelBlock())->content("Large\n30 cols")->width(30);

$panel5->layout('horizontal')
    ->border(true)
    ->dividers(true)
    ->setSizes([10, 20, 30])
    ->addBlock($smallBlock)
    ->addBlock($mediumBlock)
    ->addBlock($largeBlock)
    ->render();

$t->newline();

// Example 6: Using PanelBlock as a writer for other components
$t->writeStyled("6. PanelBlock as Writer for Other Components\n", [AnsiTerminal::TEXT_BOLD]);

$panel6 = new Panel();

// Create a block that will hold a table
$tableBlock = new PanelBlock();
$table = new Table($tableBlock);
$table->setHeaders('Name', 'Status')
    ->addRow('Task 1', 'Done')
    ->addRow('Task 2', 'Pending')
    ->render();

// Create a block that will hold a banner
$bannerBlock = new PanelBlock();
$banner = new Banner($bannerBlock);
$banner->render('Success!', ['All tasks completed']);

$panel6->layout('vertical')
    ->border(true)
    ->dividers(true)
    ->addBlock($tableBlock)
    ->addBlock($bannerBlock)
    ->render();

$t->newline();

// Example 7: Nested panels (Panel inside Panel)
$t->writeStyled("7. Dashboard Layout\n", [AnsiTerminal::TEXT_BOLD]);

$dashboard = new Panel();

// Header
$headerBlock = (new PanelBlock())
    ->content('Dashboard - System Status')
    ->width(60)
    ->border(false);

// Stats row (horizontal panel)
$statsPanel = new Panel();
$stat1 = (new PanelBlock())->content("CPU\n45%")->width(18);
$stat2 = (new PanelBlock())->content("Memory\n2.3GB")->width(18);
$stat3 = (new PanelBlock())->content("Disk\n120GB")->width(18);

$statsPanel->layout('horizontal')
    ->dividers(true, '│')
    ->addBlock($stat1)
    ->addBlock($stat2)
    ->addBlock($stat3)
    ->render();

// Footer
$footerBlock = (new PanelBlock())
    ->content('Last updated: 2025-11-28 19:33')
    ->width(60);

$dashboard->layout('vertical')
    ->border(true)
    ->dividers(true)
    ->addBlock($headerBlock)
    ->render();

$t->newline();
$t->writeStyled("🎉 Panel examples complete!\n", [AnsiTerminal::TEXT_BOLD, AnsiTerminal::FG_GREEN]);
