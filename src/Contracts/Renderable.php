<?php

declare(strict_types=1);

namespace Ajaxray\AnsiKit\Contracts;

/**
 * Interface for components that can be rendered as panel content.
 * Implementors can be nested within Panel components.
 */
interface Renderable
{
    /**
     * Render the component as an array of lines.
     * Each line should be properly formatted and padded.
     *
     * @return list<string>
     */
    public function renderLines(): array;

    /**
     * Get the total width including any borders or padding.
     */
    public function getTotalWidth(): int;

    /**
     * Get the total height including any borders or padding.
     */
    public function getTotalHeight(): int;

    /**
     * Get the content width excluding borders.
     */
    public function getContentWidth(): int;
}