<?php

declare(strict_types=1);

namespace Pest\Stressless\Result;

use Countable;

/**
 * @internal
 *
 * @property-read int $count
 * @property-read float $rate
 */
final readonly class Rate implements Countable
{
    /**
     * Creates a new duration instance.
     *
     * @param  array{rate: float, passes: int, fails: int}|array{rate: float, count: int}  $asArray
     */
    public function __construct(private array $asArray)
    {
        //
    }

    /**
     * Returns the rate.
     */
    public function rate(): float
    {
        return $this->asArray['rate'];
    }

    /**
     * Returns the count.
     */
    public function count(): int
    {
        return $this->asArray['passes'] ?? $this->asArray['count']; // @phpstan-ignore-line
    }

    /**
     * Proxies the properties to methods.
     */
    public function __get(string $name): mixed
    {
        return $this->{$name}(); // @phpstan-ignore-line
    }
}
