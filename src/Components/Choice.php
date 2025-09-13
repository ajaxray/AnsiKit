<?php

declare(strict_types=1);

namespace Ajaxray\AnsiKit\Components;

use Ajaxray\AnsiKit\AnsiTerminal;
use Ajaxray\AnsiKit\Contracts\WriterInterface;
use Ajaxray\AnsiKit\Support\Input;

/**
 * Interactive choice component that displays a numbered list of options
 * and prompts the user to select one.
 *
 * Example:
 *   $choice = new Choice();
 *   $selected = $choice->prompt('Choose an option:', ['Option A', 'Option B', 'Option C']);
 *   // Returns the selected option value or false if Exit was chosen (when required=false)
 */
final class Choice
{
    private AnsiTerminal $t;
    private bool $required = true;
    private array $promptStyle = [];
    private array $optionStyle = [];
    private array $numberStyle = [];
    private array $errorStyle = [];
    private array $exitStyle = [];

    public function __construct(?WriterInterface $writer = null)
    {
        $this->t = new AnsiTerminal($writer);
        
        // Set default styles
        $this->promptStyle = [AnsiTerminal::TEXT_BOLD, AnsiTerminal::FG_CYAN];
        $this->optionStyle = [];
        $this->numberStyle = [AnsiTerminal::FG_YELLOW];
        $this->errorStyle = [AnsiTerminal::FG_RED];
        $this->exitStyle = [AnsiTerminal::FG_BRIGHT_BLACK];
    }

    /**
     * Set whether the choice is required (defaults to true).
     * When false, an "Exit" option is automatically added.
     */
    public function required(bool $required = true): self
    {
        $this->required = $required;
        return $this;
    }

    /**
     * Set the style for the prompt message.
     */
    public function promptStyle(array $styles): self
    {
        $this->promptStyle = $styles;
        return $this;
    }

    /**
     * Set the style for option text.
     */
    public function optionStyle(array $styles): self
    {
        $this->optionStyle = $styles;
        return $this;
    }

    /**
     * Set the style for option numbers.
     */
    public function numberStyle(array $styles): self
    {
        $this->numberStyle = $styles;
        return $this;
    }

    /**
     * Set the style for error messages.
     */
    public function errorStyle(array $styles): self
    {
        $this->errorStyle = $styles;
        return $this;
    }

    /**
     * Set the style for the Exit option.
     */
    public function exitStyle(array $styles): self
    {
        $this->exitStyle = $styles;
        return $this;
    }

    /**
     * Display the choice prompt and return the selected option.
     *
     * @param string $prompt The prompt message to display
     * @param array $options Array of option strings
     * @return string|false Returns the selected option value, or false if Exit was chosen
     * @throws \InvalidArgumentException If options array is empty
     */
    public function prompt(string $prompt, array $options): string|false
    {
        if (empty($options)) {
            throw new \InvalidArgumentException('Options array cannot be empty');
        }

        // Prepare options with Exit option if not required
        $displayOptions = $options;
        if (!$this->required) {
            $displayOptions[] = 'Exit';
        }

        while (true) {
            // Display prompt
            $this->t->writeStyled($prompt, $this->promptStyle)->newline();

            // Display options
            foreach ($displayOptions as $index => $option) {
                $number = $index + 1;
                $this->t->writeStyled((string) $number, $this->numberStyle);
                $this->t->write('. ');
                
                // Use exit style for the Exit option
                if (!$this->required && $index === count($displayOptions) - 1) {
                    $this->t->writeStyled($option, $this->exitStyle);
                } else {
                    $this->t->writeStyled($option, $this->optionStyle);
                }
                $this->t->newline();
            }

            // Get user input
            $this->t->newline();
            $input = Input::line('Enter your choice: ');
            
            // Validate input
            $choice = $this->validateInput($input, count($displayOptions));
            
            if ($choice !== null) {
                // Check if Exit was selected
                if (!$this->required && $choice === count($displayOptions)) {
                    return false;
                }
                
                // Return the selected option (1-based index converted to 0-based)
                return $options[$choice - 1];
            }

            // Invalid input - show error and try again
            $this->t->writeStyled(
                "Invalid choice. Please enter a number between 1 and " . count($displayOptions) . ".\n",
                $this->errorStyle
            );
            $this->t->newline();
        }
    }

    /**
     * Validate user input and return the choice number or null if invalid.
     */
    private function validateInput(string $input, int $maxOptions): ?int
    {
        $input = trim($input);

        // Check if input is empty
        if ($input === '') {
            return null;
        }

        // Check if input contains only digits (no decimals, signs, etc.)
        if (!ctype_digit($input)) {
            return null;
        }

        $choice = (int) $input;

        // Check if choice is within valid range
        if ($choice < 1 || $choice > $maxOptions) {
            return null;
        }

        return $choice;
    }
}
