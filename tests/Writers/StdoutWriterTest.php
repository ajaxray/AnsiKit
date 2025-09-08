<?php

declare(strict_types=1);

namespace Writers;

use Ajaxray\AnsiKit\Contracts\WriterInterface;
use Ajaxray\AnsiKit\Writers\StdoutWriter;
use PHPUnit\Framework\TestCase;

final class StdoutWriterTest extends TestCase
{
    public function testImplementsWriterInterface(): void
    {
        $writer = new StdoutWriter();
        $this->assertInstanceOf(WriterInterface::class, $writer);
    }

    public function testDefaultConstructorUsesStdout(): void
    {
        $writer = new StdoutWriter();

        // We can't easily capture output from a file handle to php://stdout
        // So we just test that the writer works and returns correct byte count
        $bytesWritten = $writer->write('test output');

        $this->assertSame(11, $bytesWritten); // 'test output' is 11 bytes

        // Test that it doesn't throw any exceptions
        $this->assertIsInt($bytesWritten);
        $this->assertGreaterThan(0, $bytesWritten);
    }

    public function testConstructorWithResource(): void
    {
        $tempFile = tmpfile();
        $this->assertIsResource($tempFile);

        $writer = new StdoutWriter($tempFile);
        $bytesWritten = $writer->write('hello world');

        $this->assertSame(11, $bytesWritten);

        // Verify content was written to the resource
        rewind($tempFile);
        $content = fread($tempFile, 1024);
        $this->assertSame('hello world', $content);

        fclose($tempFile);
    }

    public function testConstructorWithValidFilePath(): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'stdout_writer_test');

        $writer = new StdoutWriter($tempFile);
        $bytesWritten = $writer->write('file content');

        $this->assertSame(12, $bytesWritten); // 'file content' is 12 bytes

        // Verify content was written to the file
        $this->assertSame('file content', file_get_contents($tempFile));

        // Clean up
        unlink($tempFile);
    }

    public function testConstructorWithInvalidFilePath(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unable to open stream: /invalid/path/that/does/not/exist');

        new StdoutWriter('/invalid/path/that/does/not/exist');
    }

    public function testConstructorWithInvalidParameterType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Stream must be a resource, path string, or null.');

        new StdoutWriter(123); // Invalid type
    }

    public function testWriteReturnsCorrectByteCount(): void
    {
        $tempFile = tmpfile();
        $writer = new StdoutWriter($tempFile);

        // Test various string lengths
        $this->assertSame(0, $writer->write(''));
        $this->assertSame(1, $writer->write('a'));
        $this->assertSame(5, $writer->write('hello'));
        $this->assertSame(13, $writer->write('hello, world!'));

        // Test with multibyte characters
        $this->assertGreaterThan(4, $writer->write('café')); // 4 characters, 6 bytes in UTF-8

        fclose($tempFile);
    }

    public function testWriteWithEmptyString(): void
    {
        $tempFile = tmpfile();
        $writer = new StdoutWriter($tempFile);

        $bytesWritten = $writer->write('');
        $this->assertSame(0, $bytesWritten);

        rewind($tempFile);
        $content = fread($tempFile, 1024);
        $this->assertSame('', $content);

        fclose($tempFile);
    }

    public function testMultipleWrites(): void
    {
        $tempFile = tmpfile();
        $writer = new StdoutWriter($tempFile);

        $writer->write('Hello ');
        $writer->write('World');
        $writer->write('!');

        rewind($tempFile);
        $content = fread($tempFile, 1024);
        $this->assertSame('Hello World!', $content);

        fclose($tempFile);
    }

    public function testWriteWithSpecialCharacters(): void
    {
        $tempFile = tmpfile();
        $writer = new StdoutWriter($tempFile);

        $specialChars = "Line 1\nLine 2\tTabbed\r\nWindows line ending";
        $bytesWritten = $writer->write($specialChars);

        $this->assertSame(strlen($specialChars), $bytesWritten);

        rewind($tempFile);
        $content = fread($tempFile, 1024);
        $this->assertSame($specialChars, $content);

        fclose($tempFile);
    }

    public function testDestructorDoesNotCloseStandardStreams(): void
    {
        // This test ensures that stdout/stderr streams are not closed by the destructor
        // We test by creating and destroying writers and ensuring no exceptions occur

        $writer = new StdoutWriter();
        $bytesWritten1 = $writer->write('test1');
        unset($writer); // Trigger destructor

        // Create another writer to ensure stdout is still usable
        $writer2 = new StdoutWriter();
        $bytesWritten2 = $writer2->write('test2');

        // If stdout was improperly closed, these operations would fail
        $this->assertSame(5, $bytesWritten1);
        $this->assertSame(5, $bytesWritten2);
    }
}
