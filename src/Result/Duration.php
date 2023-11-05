<?php

declare(strict_types=1);

namespace Pest\Stressless\Result;

/**
 * @internal
 *
 * @property-read float $avg
 * @property-read float $min
 * @property-read float $med
 * @property-read float $max
 * @property-read float $p90
 * @property-read float $p95
 */
final readonly class Duration
{
    /**
     * Creates a new duration instance.
     *
     * @param  array{avg: float, min: float, med: float, max: float, "p(90)": float, "p(95)": float}  $asArray
     */
    public function __construct(private array $asArray)
    {
        //
    }

    /**
     * Returns the average duration.
     */
    public function avg(): float
    {
        return $this->asArray['avg'];
    }

    /**
     * Returns the minimum duration.
     */
    public function min(): float
    {
        return $this->asArray['min'];
    }

    /**
     * Returns the median duration.
     */
    public function med(): float
    {
        return $this->asArray['med'];
    }

    /**
     * Returns the maximum duration.
     */
    public function max(): float
    {
        return $this->asArray['max'];
    }

    /**
     * Returns the 90th percentile duration.
     */
    public function p90(): float
    {
        return $this->asArray['p(90)'];
    }

    /**
     * Returns the 95th percentile duration.
     */
    public function p95(): float
    {
        return $this->asArray['p(95)'];
    }

    /**
     * Proxies the properties to methods.
     */
    public function __get(string $name): mixed
    {
        return $this->{$name}(); // @phpstan-ignore-line
    }
}
