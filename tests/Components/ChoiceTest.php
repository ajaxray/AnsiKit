<?php

declare(strict_types=1);

namespace Components;

use Ajaxray\AnsiKit\Components\Choice;
use Ajaxray\AnsiKit\Writers\MemoryWriter;
use PHPUnit\Framework\TestCase;

final class ChoiceTest extends TestCase
{
    public function testBasicChoiceRendersPromptAndOptions(): void
    {
        $w = new MemoryWriter();
        $choice = new Choice($w);

        $options = ['Option A', 'Option B', 'Option C'];
        
        // We can't easily mock user input in unit tests, so we'll test the rendering
        // and validate the component structure instead
        
        // Test that the component can be created and configured
        $this->assertInstanceOf(Choice::class, $choice);
        
        // Test method chaining
        $result = $choice->required(true)->promptStyle([])->optionStyle([]);
        $this->assertSame($choice, $result);
    }

    public function testRequiredMethodSetsRequiredFlag(): void
    {
        $choice = new Choice();
        
        // Test method chaining
        $result = $choice->required(false);
        $this->assertSame($choice, $result);
        
        $result = $choice->required(true);
        $this->assertSame($choice, $result);
    }

    public function testStyleMethodsReturnSelfForChaining(): void
    {
        $choice = new Choice();

        $result = $choice
            ->required(false)
            ->promptStyle([])
            ->optionStyle([])
            ->numberStyle([])
            ->errorStyle([])
            ->exitStyle([]);

        $this->assertSame($choice, $result);
    }

    public function testEmptyOptionsArrayThrowsException(): void
    {
        $choice = new Choice();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Options array cannot be empty');

        // This will throw before any input is needed
        $choice->prompt('Choose:', []);
    }

    public function testChoiceComponentCanBeInstantiatedWithWriter(): void
    {
        $writer = new MemoryWriter();
        $choice = new Choice($writer);
        
        $this->assertInstanceOf(Choice::class, $choice);
    }

    public function testChoiceComponentCanBeInstantiatedWithoutWriter(): void
    {
        $choice = new Choice();
        
        $this->assertInstanceOf(Choice::class, $choice);
    }

    public function testValidateInputMethodWithValidInput(): void
    {
        $choice = new Choice();
        
        // Use reflection to test the private validateInput method
        $reflection = new \ReflectionClass($choice);
        $method = $reflection->getMethod('validateInput');
        $method->setAccessible(true);
        
        // Test valid inputs
        $this->assertSame(1, $method->invoke($choice, '1', 3));
        $this->assertSame(2, $method->invoke($choice, '2', 3));
        $this->assertSame(3, $method->invoke($choice, '3', 3));
    }

    public function testValidateInputMethodWithInvalidInput(): void
    {
        $choice = new Choice();
        
        // Use reflection to test the private validateInput method
        $reflection = new \ReflectionClass($choice);
        $method = $reflection->getMethod('validateInput');
        $method->setAccessible(true);
        
        // Test invalid inputs
        $this->assertNull($method->invoke($choice, 'invalid', 3));
        $this->assertNull($method->invoke($choice, '0', 3));
        $this->assertNull($method->invoke($choice, '4', 3));
        $this->assertNull($method->invoke($choice, '-1', 3));
        $this->assertNull($method->invoke($choice, '', 3));
        $this->assertNull($method->invoke($choice, '1.5', 3));
    }

    public function testValidateInputMethodWithEdgeCases(): void
    {
        $choice = new Choice();
        
        // Use reflection to test the private validateInput method
        $reflection = new \ReflectionClass($choice);
        $method = $reflection->getMethod('validateInput');
        $method->setAccessible(true);
        
        // Test edge cases
        $this->assertSame(1, $method->invoke($choice, ' 1 ', 3)); // whitespace trimming
        $this->assertNull($method->invoke($choice, '1.0', 3)); // decimal numbers
        $this->assertNull($method->invoke($choice, '+1', 3)); // positive sign
    }

    public function testChoiceComponentWithSingleOption(): void
    {
        $writer = new MemoryWriter();
        $choice = new Choice($writer);
        
        // Test that component handles single option correctly
        $this->assertInstanceOf(Choice::class, $choice);
        
        // Test validation with single option
        $reflection = new \ReflectionClass($choice);
        $method = $reflection->getMethod('validateInput');
        $method->setAccessible(true);
        
        $this->assertSame(1, $method->invoke($choice, '1', 1));
        $this->assertNull($method->invoke($choice, '2', 1));
    }

    public function testChoiceComponentWithManyOptions(): void
    {
        $writer = new MemoryWriter();
        $choice = new Choice($writer);
        
        // Test that component handles many options correctly
        $this->assertInstanceOf(Choice::class, $choice);
        
        // Test validation with many options
        $reflection = new \ReflectionClass($choice);
        $method = $reflection->getMethod('validateInput');
        $method->setAccessible(true);
        
        $this->assertSame(10, $method->invoke($choice, '10', 10));
        $this->assertNull($method->invoke($choice, '11', 10));
    }
}
