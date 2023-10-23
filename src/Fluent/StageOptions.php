<?php

declare(strict_types=1);

namespace Pest\Stressless\Fluent;

use Pest\Stressless\Factory;

/**
 * @internal
 */
final readonly class StageOptions
{
    /**
     * Creates a new stage options instance.
     */
    public function __construct(
        private Factory $factory,
        private int $requests,
    ) {
        //
    }

    /**
     * Specifies that the stage should run for the given duration.
     */
    public function for(int $duration): StageDurationOptions
    {
        return new StageDurationOptions($this->factory, $this->requests, $duration);
    }
}
