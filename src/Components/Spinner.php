<?php

declare(strict_types=1);

namespace Ajaxray\AnsiKit\Components;

/**
 * Lightweight spinner component for CLI animations.
 *
 * Usage:
 *   $spinner = new Spinner();              // default dotted
 *   $spinner = new Spinner(Spinner::ASCII); // ASCII variant: | / - \
 *   echo $spinner->next();                 // get next frame
 */
final class Spinner
{
    public const DOTS = 'dots';
    public const ASCII = 'ascii';
    public const BOX = 'box';

    /** @var string[] */
    private array $frames = [];
    private int $index = 0;

    public function __construct(string $variant = self::DOTS)
    {
        $this->setVariant($variant);
    }

    /** @return $this */
    public function setVariant(string $variant): self
    {
        switch ($variant) {
            case self::ASCII:
                $this->frames = ['|', '/', '-', '\\'];
                break;
            case self::DOTS:
            default:
                $this->frames = ['⠋','⠙','⠚','⠞','⠖','⠦','⠴','⠲','⠳','⠓'];
                break;
        }
        $this->index = 0;
        return $this;
    }

    /** Supply custom frames; resets index. @param string[] $frames */
    public function setFrames(array $frames): self
    {
        if (empty($frames)) {
            throw new \InvalidArgumentException('Spinner frames cannot be empty');
        }
        $this->frames = array_values($frames);
        $this->index = 0;
        return $this;
    }

    /** Get the next frame and advance the index. */
    public function next(): string
    {
        $frame = $this->frames[$this->index];
        $this->index = $this->index + 1;
        if ($this->index >= count($this->frames)) {
            $this->index = 0;
        }

        return $frame;
    }

    /** Deterministic frame by position (supports negatives). */
    public function frameAt(int $position): string
    {
        $count = count($this->frames);
        if ($count === 0) {
            return '';
        }
        $i = $position % $count;
        if ($i < 0) {
            $i += $count;
        }
        return $this->frames[$i];
    }

    /** Reset the spinner back to the first frame. @return $this */
    public function reset(): self
    {
        $this->index = 0;
        return $this;
    }

    /** @return string[] */
    public function getFrames(): array
    {
        return $this->frames;
    }
}
