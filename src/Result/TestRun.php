<?php

declare(strict_types=1);

namespace Pest\Stressless\Result;

use Pest\Stressless\Result;

/**
 * @internal
 *
 * @property-read float $duration
 * @property-read int   $concurrency
 */
final readonly class TestRun
{
    /**
     * Creates a new requests instance.
     */
    public function __construct(private Result $result)
    {
        //
    }

    /**
     * Returns the test run duration.
     */
    public function duration(): float
    {
        return $this->result->toArray()['state']['testRunDurationMs'];
    }

    /**
     * Returns the test run concurrency.
     */
    public function concurrency(): int
    {
        return $this->result->toArray()['metrics']['vus_max']['values']['value'];
    }

    /**
     * Proxies the properties to methods.
     */
    public function __get(string $name): mixed
    {
        return $this->{$name}(); // @phpstan-ignore-line
    }
}
