<?php

declare(strict_types=1);

namespace Pest\Stressless\Contracts;

/**
 * @internal
 */
interface Block
{
    /**
     * Gets the block value.
     */
    public function value(): string;

    /**
     * Gets the block color.
     */
    public function color(): string;
}
