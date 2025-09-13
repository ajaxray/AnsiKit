<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Ajaxray\AnsiKit\AnsiTerminal;
use Ajaxray\AnsiKit\Components\Choice;

$t = new AnsiTerminal();
$t->clearScreen()->cursorHome();

$t->writeStyled("📋 Interactive Menu System\n", [AnsiTerminal::TEXT_BOLD, AnsiTerminal::FG_CYAN]);
$t->writeStyled("Powered by AnsiKit Choice Component\n", [AnsiTerminal::FG_BRIGHT_BLACK]);
$t->newline();

// Main menu loop
while (true) {
    $choice = new Choice();
    $selected = $choice
        ->required(false) // This adds the Exit option
        ->promptStyle([AnsiTerminal::TEXT_BOLD, AnsiTerminal::FG_CYAN])
        ->optionStyle([AnsiTerminal::FG_WHITE])
        ->numberStyle([AnsiTerminal::TEXT_BOLD, AnsiTerminal::FG_YELLOW])
        ->exitStyle([AnsiTerminal::FG_RED])
        ->prompt('🚀 Main Menu - Choose an action:', [
            'View System Status',
            'Manage Users',
            'Configure Settings',
            'View Reports',
            'Run Diagnostics'
        ]);

    $t->newline();

    // Handle the selection
    if ($selected === false) {
        $t->writeStyled("👋 Goodbye! Thanks for using the system.\n", [AnsiTerminal::FG_CYAN]);
        break;
    }

    // Process the selected option
    switch ($selected) {
        case 'View System Status':
            handleSystemStatus($t);
            break;
        case 'Manage Users':
            handleUserManagement($t);
            break;
        case 'Configure Settings':
            handleSettings($t);
            break;
        case 'View Reports':
            handleReports($t);
            break;
        case 'Run Diagnostics':
            handleDiagnostics($t);
            break;
    }

    $t->newline();
    $t->writeStyled("Press Enter to return to main menu...", [AnsiTerminal::FG_BRIGHT_BLACK]);
    fgets(STDIN);
    $t->clearScreen()->cursorHome();
}

function handleSystemStatus(AnsiTerminal $t): void
{
    $t->writeStyled("📊 System Status\n", [AnsiTerminal::TEXT_BOLD, AnsiTerminal::FG_GREEN]);
    $t->write("• CPU Usage: 45%\n");
    $t->write("• Memory Usage: 62%\n");
    $t->write("• Disk Usage: 78%\n");
    $t->write("• Network: Connected\n");
    $t->writeStyled("✅ All systems operational\n", [AnsiTerminal::FG_GREEN]);
}

function handleUserManagement(AnsiTerminal $t): void
{
    $t->writeStyled("👥 User Management\n", [AnsiTerminal::TEXT_BOLD, AnsiTerminal::FG_BLUE]);
    
    $choice = new Choice();
    $action = $choice
        ->required(false)
        ->prompt('Choose user action:', [
            'List Users',
            'Add User',
            'Delete User',
            'Modify Permissions'
        ]);

    if ($action === false) {
        $t->writeStyled("❌ User management cancelled.\n", [AnsiTerminal::FG_YELLOW]);
        return;
    }

    $t->writeStyled("✅ Selected: {$action}\n", [AnsiTerminal::FG_GREEN]);
    $t->write("(This would execute the {$action} functionality)\n");
}

function handleSettings(AnsiTerminal $t): void
{
    $t->writeStyled("⚙️  Configuration Settings\n", [AnsiTerminal::TEXT_BOLD, AnsiTerminal::FG_MAGENTA]);
    
    $choice = new Choice();
    $setting = $choice->prompt('Choose setting to configure:', [
        'Database Connection',
        'Email Settings',
        'Security Options',
        'Logging Level'
    ]);

    $t->writeStyled("✅ Configuring: {$setting}\n", [AnsiTerminal::FG_GREEN]);
    $t->write("(Configuration interface would open here)\n");
}

function handleReports(AnsiTerminal $t): void
{
    $t->writeStyled("📈 Reports\n", [AnsiTerminal::TEXT_BOLD, AnsiTerminal::FG_YELLOW]);
    $t->write("• Daily Activity Report: Available\n");
    $t->write("• Weekly Summary: Available\n");
    $t->write("• Monthly Analytics: Available\n");
    $t->write("• Custom Reports: 3 available\n");
}

function handleDiagnostics(AnsiTerminal $t): void
{
    $t->writeStyled("🔧 Running Diagnostics...\n", [AnsiTerminal::TEXT_BOLD, AnsiTerminal::FG_CYAN]);
    
    // Simulate diagnostic process
    $tests = [
        'Database Connection',
        'File System Permissions',
        'Network Connectivity',
        'Service Dependencies'
    ];

    foreach ($tests as $test) {
        $t->write("Testing {$test}... ");
        usleep(500000); // 0.5 second delay
        $t->writeStyled("✅ PASS\n", [AnsiTerminal::FG_GREEN]);
    }
    
    $t->writeStyled("🎉 All diagnostics passed!\n", [AnsiTerminal::TEXT_BOLD, AnsiTerminal::FG_GREEN]);
}
