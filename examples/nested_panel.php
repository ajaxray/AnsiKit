<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Ajaxray\AnsiKit\AnsiTerminal;
use Ajaxray\AnsiKit\Components\Panel;
use Ajaxray\AnsiKit\Components\PanelBlock;
use Ajaxray\AnsiKit\Components\Table;

$t = new AnsiTerminal();
$t->clearScreen()->cursorHome();

$t->writeStyled("Nested Panel Examples\n", [AnsiTerminal::TEXT_BOLD, AnsiTerminal::FG_CYAN]);
$t->newline();

// Example 1: Simple nested panel - Horizontal panel inside vertical panel
$t->writeStyled("1. Horizontal Panel Nested in Vertical Panel\n", [AnsiTerminal::TEXT_BOLD]);

$mainPanel = new Panel();

// Create nested horizontal panel
// Nested panel width: 3 blocks (13 each) + 2 dividers + 2 borders = 13*3 + 2 + 2 = 43
$nestedPanel = new Panel();
$nestedPanel->layout(Panel::LAYOUT_HORIZONTAL)
    ->border(true)
    ->dividers(true)
    ->addBlock((new PanelBlock())->content('Column 1')->width(13))
    ->addBlock((new PanelBlock())->content('Column 2')->width(13))
    ->addBlock((new PanelBlock())->content('Column 3')->width(13));

// Add to main vertical panel
// Parent panel content width will be 43 (nested panel's total width)
$mainPanel->layout(Panel::LAYOUT_VERTICAL)
    ->border(true)
    ->dividers(true)
    ->addBlock((new PanelBlock())->content('Header Section'))
    ->addBlock($nestedPanel)
    ->addBlock((new PanelBlock())->content('Footer Section'))
    ->render();

$t->newline();

// Example 2: Dashboard layout with multiple nested panels
$t->writeStyled("2. Dashboard Layout with Multiple Nested Panels\n", [AnsiTerminal::TEXT_BOLD]);

$dashboard = new Panel();

// Header
$header = (new PanelBlock())
    ->content('System Dashboard - Real-time Monitoring')
    ->width(60);

// Stats row (horizontal panel with 3 stats)
$statsPanel = new Panel();
$statsPanel->layout(Panel::LAYOUT_HORIZONTAL)
    ->border(true)
    ->dividers(true)
    ->addBlock((new PanelBlock())->content("CPU Usage\n   45%\n  Normal")->width(18))
    ->addBlock((new PanelBlock())->content("Memory\n  2.3GB\n  Normal")->width(18))
    ->addBlock((new PanelBlock())->content("Disk I/O\n  120MB/s\n   High")->width(18));

// Metrics row (horizontal panel with 2 sections)
$metricsPanel = new Panel();
$metricsPanel->layout(Panel::LAYOUT_HORIZONTAL)
    ->border(true)
    ->dividers(true)
    ->addBlock((new PanelBlock())->content("Active Users\n     1,234\n   +12.5%")->width(28))
    ->addBlock((new PanelBlock())->content("Requests/sec\n       456\n    +8.3%")->width(28));

// Footer
$footer = (new PanelBlock())
    ->content('Last updated: 2025-12-06 17:30:00 | Status: All systems operational')
    ->width(60);

$dashboard->layout(Panel::LAYOUT_VERTICAL)
    ->border(true)
    ->dividers(true)
    ->corners(Panel::CORNER_ROUNDED)
    ->addBlock($header)
    ->addBlock($statsPanel)
    ->addBlock($metricsPanel)
    ->addBlock($footer)
    ->render();

$t->newline();

// Example 3: Vertical panels nested in horizontal layout
$t->writeStyled("3. Vertical Panels Nested in Horizontal Layout\n", [AnsiTerminal::TEXT_BOLD]);

$horizontalMain = new Panel();

// Left sidebar (vertical panel)
$leftPanel = new Panel();
$leftPanel->layout(Panel::LAYOUT_VERTICAL)
    ->border(true)
    ->dividers(true)
    ->addBlock((new PanelBlock())->content('Menu')->width(15))
    ->addBlock((new PanelBlock())->content('Home')->width(15))
    ->addBlock((new PanelBlock())->content('Settings')->width(15))
    ->addBlock((new PanelBlock())->content('Logout')->width(15));

// Main content (vertical panel)
$contentPanel = new Panel();
$contentPanel->layout(Panel::LAYOUT_VERTICAL)
    ->border(true)
    ->dividers(true)
    ->addBlock((new PanelBlock())->content('Main Content Area')->width(35))
    ->addBlock((new PanelBlock())->content('Lorem ipsum dolor sit amet')->width(35))
    ->addBlock((new PanelBlock())->content('consectetur adipiscing')->width(35));

$horizontalMain->layout(Panel::LAYOUT_HORIZONTAL)
    ->border(true)
    ->dividers(true)
    ->corners(Panel::CORNER_ROUNDED)
    ->addBlock($leftPanel)
    ->addBlock($contentPanel)
    ->render();

$t->newline();

// Example 4: Complex nested structure with components
$t->writeStyled("4. Complex Nested Structure with Table Component\n", [AnsiTerminal::TEXT_BOLD]);

$complexPanel = new Panel();

// Title
$title = (new PanelBlock())
    ->content('Project Status Report')
    ->width(50)
    ->border(false);

// Table in a block
$tableBlock = new PanelBlock();
$table = new Table($tableBlock);
$table->setHeaders('Task', 'Status', 'Progress')
    ->addRow('Backend API', 'Done', '100%')
    ->addRow('Frontend UI', 'In Progress', '75%')
    ->addRow('Testing', 'Pending', '0%')
    ->render();

// Summary panel (horizontal)
$summaryPanel = new Panel();
$summaryPanel->layout(Panel::LAYOUT_HORIZONTAL)
    ->border(true)
    ->dividers(true)
    ->addBlock((new PanelBlock())->content("Total\n  3")->width(14))
    ->addBlock((new PanelBlock())->content("Done\n  1")->width(14))
    ->addBlock((new PanelBlock())->content("Pending\n   2")->width(14));

$complexPanel->layout(Panel::LAYOUT_VERTICAL)
    ->border(true)
    ->dividers(true)
    ->corners(Panel::CORNER_SHARP)
    ->addBlock($title)
    ->addBlock($tableBlock)
    ->addBlock($summaryPanel)
    ->render();

$t->newline();

// Example 5: Three-level nesting
$t->writeStyled("5. Three-Level Nested Panels\n", [AnsiTerminal::TEXT_BOLD]);

$level1 = new Panel();

// Level 2 - nested panel
$level2 = new Panel();

// Level 3 - deeply nested panel
$level3 = new Panel();
$level3->layout(Panel::LAYOUT_HORIZONTAL)
    ->border(true)
    ->dividers(true)
    ->corners(Panel::CORNER_ROUNDED)
    ->addBlock((new PanelBlock())->content('L3-A')->width(8))
    ->addBlock((new PanelBlock())->content('L3-B')->width(8));

$level2->layout(Panel::LAYOUT_VERTICAL)
    ->border(true)
    ->dividers(true)
    ->addBlock((new PanelBlock())->content('Level 2 Header')->width(20))
    ->addBlock($level3)
    ->addBlock((new PanelBlock())->content('Level 2 Footer')->width(20));

$level1->layout(Panel::LAYOUT_VERTICAL)
    ->border(true)
    ->dividers(true)
    ->corners(Panel::CORNER_SHARP)
    ->addBlock((new PanelBlock())->content('Level 1 - Top')->width(22))
    ->addBlock($level2)
    ->addBlock((new PanelBlock())->content('Level 1 - Bottom')->width(22))
    ->render();

$t->newline();
$t->writeStyled("🎉 Nested panel examples complete!\n", [AnsiTerminal::TEXT_BOLD, AnsiTerminal::FG_GREEN]);