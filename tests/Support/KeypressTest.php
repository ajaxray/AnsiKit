<?php

declare(strict_types=1);

namespace Support;

use Ajaxray\AnsiKit\Support\Keypress;
use PHPUnit\Framework\TestCase;

final class KeypressTest extends TestCase
{
    public function testKeypressConstants(): void
    {
        // Test basic key constants
        $this->assertSame('UP', Keypress::KEY_UP);
        $this->assertSame('DOWN', Keypress::KEY_DOWN);
        $this->assertSame('RIGHT', Keypress::KEY_RIGHT);
        $this->assertSame('LEFT', Keypress::KEY_LEFT);
        $this->assertSame('ENTER', Keypress::KEY_ENTER);
        $this->assertSame('SPACE', Keypress::KEY_SPACE);
        $this->assertSame('BACKSPACE', Keypress::KEY_BACKSPACE);
        $this->assertSame('TAB', Keypress::KEY_TAB);
        $this->assertSame('ESC', Keypress::KEY_ESC);

        // Test Ctrl key constants
        $this->assertSame('CTRL+A', Keypress::KEY_CTRL_A);
        $this->assertSame('CTRL+C', Keypress::KEY_CTRL_C);
        $this->assertSame('CTRL+Z', Keypress::KEY_CTRL_Z);

        // Test function key constants
        $this->assertSame('F1', Keypress::KEY_F1);
        $this->assertSame('F12', Keypress::KEY_F12);

        // Test modified arrow key constants
        $this->assertSame('CTRL+UP', Keypress::KEY_CTRL_UP);
        $this->assertSame('ALT+DOWN', Keypress::KEY_ALT_DOWN);
        $this->assertSame('SHIFT+RIGHT', Keypress::KEY_SHIFT_RIGHT);

        // Test navigation key constants
        $this->assertSame('HOME', Keypress::KEY_HOME);
        $this->assertSame('END', Keypress::KEY_END);
        $this->assertSame('DELETE', Keypress::KEY_DELETE);
    }

    public function testTranslateKeyWithArrowKeys(): void
    {
        $reflection = new \ReflectionClass(Keypress::class);
        $method = $reflection->getMethod('translateKey');
        $method->setAccessible(true);

        // Test basic arrow keys
        $this->assertSame(Keypress::KEY_UP, $method->invoke(null, "\033[A"));
        $this->assertSame(Keypress::KEY_DOWN, $method->invoke(null, "\033[B"));
        $this->assertSame(Keypress::KEY_RIGHT, $method->invoke(null, "\033[C"));
        $this->assertSame(Keypress::KEY_LEFT, $method->invoke(null, "\033[D"));

        // Test modified arrow keys
        $this->assertSame(Keypress::KEY_CTRL_UP, $method->invoke(null, "\033[1;5A"));
        $this->assertSame(Keypress::KEY_ALT_DOWN, $method->invoke(null, "\033[1;3B"));
        $this->assertSame(Keypress::KEY_SHIFT_RIGHT, $method->invoke(null, "\033[1;2C"));
    }

    public function testTranslateKeyWithSpecialKeys(): void
    {
        $reflection = new \ReflectionClass(Keypress::class);
        $method = $reflection->getMethod('translateKey');
        $method->setAccessible(true);

        // Test special keys
        $this->assertSame(Keypress::KEY_ENTER, $method->invoke(null, "\n"));
        $this->assertSame(Keypress::KEY_SPACE, $method->invoke(null, " "));
        $this->assertSame(Keypress::KEY_BACKSPACE, $method->invoke(null, "\010"));
        $this->assertSame(Keypress::KEY_BACKSPACE, $method->invoke(null, "\177"));
        $this->assertSame(Keypress::KEY_TAB, $method->invoke(null, "\t"));
        $this->assertSame(Keypress::KEY_ESC, $method->invoke(null, "\e"));
    }

    public function testTranslateKeyWithRegularCharacters(): void
    {
        $reflection = new \ReflectionClass(Keypress::class);
        $method = $reflection->getMethod('translateKey');
        $method->setAccessible(true);

        // Test regular characters (should return as-is)
        $this->assertSame('a', $method->invoke(null, 'a'));
        $this->assertSame('A', $method->invoke(null, 'A'));
        $this->assertSame('1', $method->invoke(null, '1'));
        $this->assertSame('!', $method->invoke(null, '!'));
        $this->assertSame('@', $method->invoke(null, '@'));
    }

    public function testTranslateKeyWithUnknownSequences(): void
    {
        $reflection = new \ReflectionClass(Keypress::class);
        $method = $reflection->getMethod('translateKey');
        $method->setAccessible(true);

        // Test unknown escape sequences (should return as-is)
        $this->assertSame("\033[Z", $method->invoke(null, "\033[Z"));
        $this->assertSame("\033[1;5A", $method->invoke(null, "\033[1;5A"));
        $this->assertSame("\033OP", $method->invoke(null, "\033OP"));
    }

    public function testTranslateKeyWithEmptyString(): void
    {
        $reflection = new \ReflectionClass(Keypress::class);
        $method = $reflection->getMethod('translateKey');
        $method->setAccessible(true);

        // Test empty string
        $this->assertSame('', $method->invoke(null, ''));
    }

    public function testTranslateKeyWithControlCharacters(): void
    {
        $reflection = new \ReflectionClass(Keypress::class);
        $method = $reflection->getMethod('translateKey');
        $method->setAccessible(true);

        // Test Ctrl combinations that are now handled
        $this->assertSame(Keypress::KEY_CTRL_A, $method->invoke(null, "\x01"));
        $this->assertSame(Keypress::KEY_CTRL_C, $method->invoke(null, "\x03"));
        $this->assertSame(Keypress::KEY_CTRL_Z, $method->invoke(null, "\x1A"));
        $this->assertSame(Keypress::KEY_CTRL_S, $method->invoke(null, "\x13"));
        $this->assertSame(Keypress::KEY_CTRL_V, $method->invoke(null, "\x16"));
    }

    public function testKeypressListenMethodExists(): void
    {
        // Test that the listen method exists and is callable
        $this->assertTrue(method_exists(Keypress::class, 'listen'));
        $this->assertTrue(is_callable([Keypress::class, 'listen']));
    }

    public function testKeypressListenMethodIsStatic(): void
    {
        // Test that the listen method is static
        $reflection = new \ReflectionClass(Keypress::class);
        $method = $reflection->getMethod('listen');
        $this->assertTrue($method->isStatic());
    }

    public function testKeypressListenMethodReturnType(): void
    {
        // Test that the listen method returns string
        $reflection = new \ReflectionClass(Keypress::class);
        $method = $reflection->getMethod('listen');
        $returnType = $method->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertSame('string', $returnType->getName());
    }

    public function testTranslateKeyMethodIsPrivate(): void
    {
        // Test that translateKey method is private
        $reflection = new \ReflectionClass(Keypress::class);
        $method = $reflection->getMethod('translateKey');
        $this->assertTrue($method->isPrivate());
    }

    public function testTranslateKeyMethodIsStatic(): void
    {
        // Test that translateKey method is static
        $reflection = new \ReflectionClass(Keypress::class);
        $method = $reflection->getMethod('translateKey');
        $this->assertTrue($method->isStatic());
    }

    public function testKeypressClassIsFinal(): void
    {
        // Test that Keypress class is final
        $reflection = new \ReflectionClass(Keypress::class);
        $this->assertTrue($reflection->isFinal());
    }

    public function testKeypressConstantsArePublic(): void
    {
        // Test that all constants are public
        $reflection = new \ReflectionClass(Keypress::class);
        $constants = $reflection->getConstants();
        
        $expectedConstants = [
            'KEY_UP', 'KEY_DOWN', 'KEY_RIGHT', 'KEY_LEFT',
            'KEY_ENTER', 'KEY_SPACE', 'KEY_BACKSPACE', 'KEY_TAB', 'KEY_ESC'
        ];
        
        foreach ($expectedConstants as $constantName) {
            $this->assertArrayHasKey($constantName, $constants);
        }
    }

    public function testKeypressConstantsHaveCorrectValues(): void
    {
        // Test that constants have expected string values
        $expectedValues = [
            'KEY_UP' => 'UP',
            'KEY_DOWN' => 'DOWN',
            'KEY_RIGHT' => 'RIGHT',
            'KEY_LEFT' => 'LEFT',
            'KEY_ENTER' => 'ENTER',
            'KEY_SPACE' => 'SPACE',
            'KEY_BACKSPACE' => 'BACKSPACE',
            'KEY_TAB' => 'TAB',
            'KEY_ESC' => 'ESC'
        ];

        $reflection = new \ReflectionClass(Keypress::class);
        $constants = $reflection->getConstants();

        foreach ($expectedValues as $constantName => $expectedValue) {
            $this->assertSame($expectedValue, $constants[$constantName]);
        }
    }

    /**
     * Test that demonstrates the expected behavior when using Keypress constants
     * in conditional logic (common use case)
     */
    public function testKeypressConstantsInConditionalLogic(): void
    {
        // Simulate what translateKey would return for various inputs
        $reflection = new \ReflectionClass(Keypress::class);
        $method = $reflection->getMethod('translateKey');
        $method->setAccessible(true);

        $upKey = $method->invoke(null, "\033[A");
        $enterKey = $method->invoke(null, "\n");
        $escKey = $method->invoke(null, "\e");

        // Test that we can use constants in conditional logic
        $this->assertTrue($upKey === Keypress::KEY_UP);
        $this->assertTrue($enterKey === Keypress::KEY_ENTER);
        $this->assertTrue($escKey === Keypress::KEY_ESC);

        // Test switch-case scenario
        $action = match ($upKey) {
            Keypress::KEY_UP => 'move_up',
            Keypress::KEY_DOWN => 'move_down',
            Keypress::KEY_ENTER => 'select',
            Keypress::KEY_ESC => 'exit',
            default => 'unknown'
        };

        $this->assertSame('move_up', $action);
    }

    public function testGetKeyNameMethod(): void
    {
        // Test basic keys
        $this->assertSame("UP ARROW", Keypress::getKeyName(Keypress::KEY_UP));
        $this->assertSame("ENTER", Keypress::getKeyName(Keypress::KEY_ENTER));
        $this->assertSame("ESCAPE", Keypress::getKeyName(Keypress::KEY_ESC));

        // Test Ctrl combinations
        $this->assertSame("CTRL+A", Keypress::getKeyName(Keypress::KEY_CTRL_A));
        $this->assertSame("CTRL+C", Keypress::getKeyName(Keypress::KEY_CTRL_C));

        // Test function keys
        $this->assertSame("F1", Keypress::getKeyName(Keypress::KEY_F1));
        $this->assertSame("F12", Keypress::getKeyName(Keypress::KEY_F12));

        // Test modified arrow keys
        $this->assertSame("CTRL+UP", Keypress::getKeyName(Keypress::KEY_CTRL_UP));
        $this->assertSame("ALT+DOWN", Keypress::getKeyName(Keypress::KEY_ALT_DOWN));

        // Test navigation keys
        $this->assertSame("HOME", Keypress::getKeyName(Keypress::KEY_HOME));
        $this->assertSame("DELETE", Keypress::getKeyName(Keypress::KEY_DELETE));

        // Test regular characters
        $this->assertSame("'a'", Keypress::getKeyName('a'));
        $this->assertSame("'1'", Keypress::getKeyName('1'));

        // Test unknown sequences
        $this->assertSame("UNKNOWN SEQUENCE", Keypress::getKeyName("\033[Z"));
    }

    public function testDetectAltKeyMethod(): void
    {
        // Test valid Alt combinations
        $this->assertSame("ALT+A", Keypress::detectAltKey("\033A"));
        $this->assertSame("ALT+1", Keypress::detectAltKey("\0331"));
        $this->assertSame("ALT+Z", Keypress::detectAltKey("\033z"));

        // Test invalid sequences
        $this->assertNull(Keypress::detectAltKey("a"));
        $this->assertNull(Keypress::detectAltKey("\033"));
        $this->assertNull(Keypress::detectAltKey("\033\x01")); // Non-printable
        $this->assertNull(Keypress::detectAltKey("\033[A")); // Arrow key sequence
    }

    public function testTranslateKeyWithFunctionKeys(): void
    {
        $reflection = new \ReflectionClass(Keypress::class);
        $method = $reflection->getMethod('translateKey');
        $method->setAccessible(true);

        // Test function keys
        $this->assertSame(Keypress::KEY_F1, $method->invoke(null, "\033OP"));
        $this->assertSame(Keypress::KEY_F2, $method->invoke(null, "\033OQ"));
        $this->assertSame(Keypress::KEY_F5, $method->invoke(null, "\033[15~"));
        $this->assertSame(Keypress::KEY_F12, $method->invoke(null, "\033[24~"));
    }

    public function testTranslateKeyWithNavigationKeys(): void
    {
        $reflection = new \ReflectionClass(Keypress::class);
        $method = $reflection->getMethod('translateKey');
        $method->setAccessible(true);

        // Test navigation keys
        $this->assertSame(Keypress::KEY_HOME, $method->invoke(null, "\033[H"));
        $this->assertSame(Keypress::KEY_END, $method->invoke(null, "\033[F"));
        $this->assertSame(Keypress::KEY_PAGE_UP, $method->invoke(null, "\033[5~"));
        $this->assertSame(Keypress::KEY_PAGE_DOWN, $method->invoke(null, "\033[6~"));
        $this->assertSame(Keypress::KEY_INSERT, $method->invoke(null, "\033[2~"));
        $this->assertSame(Keypress::KEY_DELETE, $method->invoke(null, "\033[3~"));
    }

    public function testNewMethodsExistAndArePublic(): void
    {
        // Test getKeyName method
        $this->assertTrue(method_exists(Keypress::class, 'getKeyName'));
        $this->assertTrue(is_callable([Keypress::class, 'getKeyName']));

        $reflection = new \ReflectionClass(Keypress::class);
        $getKeyNameMethod = $reflection->getMethod('getKeyName');
        $this->assertTrue($getKeyNameMethod->isStatic());
        $this->assertTrue($getKeyNameMethod->isPublic());

        // Test detectAltKey method
        $this->assertTrue(method_exists(Keypress::class, 'detectAltKey'));
        $this->assertTrue(is_callable([Keypress::class, 'detectAltKey']));

        $detectAltKeyMethod = $reflection->getMethod('detectAltKey');
        $this->assertTrue($detectAltKeyMethod->isStatic());
        $this->assertTrue($detectAltKeyMethod->isPublic());
    }
}
