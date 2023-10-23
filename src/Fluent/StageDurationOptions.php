<?php

declare(strict_types=1);

namespace Pest\Stressless\Fluent;

use Pest\Stressless\Factory;

/**
 * @internal
 */
final readonly class StageDurationOptions
{
    /**
     * Creates a new stage duration options instance.
     */
    public function __construct(
        private Factory $factory,
        private int $requests,
        private int $duration,
    ) {
        //
    }

    /**
     * Specifies that the stage should run for 1 second.
     */
    public function second(): Factory
    {
        assert($this->duration === 1, 'The duration must be 1 second.');

        return $this->seconds();
    }

    /**
     * Specifies that the stage should run for the given number of seconds.
     */
    public function seconds(): Factory
    {
        $this->factory->stage($this->requests, 0);
        $this->factory->stage($this->requests, $this->duration);

        return $this->factory;
    }

    /**
     * Specifies that the stage should run for 1 minute.
     */
    public function minute(): Factory
    {
        assert($this->duration === 1, 'The duration must be 1 minute.');

        return $this->minutes();
    }

    /**
     * Specifies that the stage should run for the given number of minutes.
     */
    public function minutes(): Factory
    {
        $this->factory->stage($this->requests, 0);
        $this->factory->stage($this->requests, $this->duration * 60);

        return $this->factory;
    }

    /**
     * Specifies that the stage should run for 1 hour.
     */
    public function hour(): Factory
    {
        assert($this->duration === 1, 'The duration must be 1 hour.');

        return $this->hours();
    }

    /**
     * Specifies that the stage should run for the given number of hours.
     */
    public function hours(): Factory
    {
        $this->factory->stage($this->requests, 0);
        $this->factory->stage($this->requests, $this->duration * 60 * 60);

        return $this->factory;
    }
}
