<?php

declare(strict_types=1);

namespace Ajaxray\AnsiKit\Writers;

use Ajaxray\AnsiKit\Contracts\WriterInterface;

final class StdoutWriter implements WriterInterface
{
    /** @var resource */
    private $stream;

    /**
     * @param resource|string|null $stream Resource or path. Defaults to php://stdout
     */
    public function __construct($stream = null)
    {
        if ($stream === null) {
            $this->stream = \fopen('php://stdout', 'w');
            return;
        }

        if (\is_resource($stream)) {
            $this->stream = $stream;
            return;
        }

        if (\is_string($stream)) {
            $h = @\fopen($stream, 'w');
            if ($h === false) {
                throw new \InvalidArgumentException("Unable to open stream: {$stream}");
            }
            $this->stream = $h;
            return;
        }

        throw new \InvalidArgumentException('Stream must be a resource, path string, or null.');
    }

    public function __destruct()
    {
        if (\is_resource($this->stream)) {
            $meta = \stream_get_meta_data($this->stream);
            if (isset($meta['uri']) && $meta['uri'] !== 'php://stdout' && $meta['uri'] !== 'php://stderr') {
                @\fclose($this->stream);
            }
        }
    }

    public function write(string $bytes): int
    {
        return \fwrite($this->stream, $bytes);
    }
}