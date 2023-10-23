<?php

declare(strict_types=1);

namespace Pest\Stressless\Fluent;

use Pest\Stressless\Factory;

/**
 * @internal
 */
final readonly class WithOptions
{
    /**
     * Creates a new with options instance.
     */
    public function __construct(
        private Factory $factory,
        private int $number,
    ) {
        //
    }

    /**
     * Creates a new stage options instance.
     */
    public function concurrentRequests(): StageOptions
    {
        return new StageOptions($this->factory, $this->number);
    }
}
