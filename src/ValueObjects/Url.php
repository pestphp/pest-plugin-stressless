<?php

declare(strict_types=1);

namespace Pest\Stressless\ValueObjects;

use Stringable;

/**
 * @internal
 */
final readonly class Url implements Stringable
{
    /**
     * The URL value.
     */
    private string $url;

    /**
     * Creates a new URL instance.
     */
    public function __construct(string $url)
    {
        $this->url = str_starts_with($url, 'http') ? $url : 'https://'.$url;
    }

    /**
     * The string representation of the URL.
     */
    public function __toString(): string
    {
        return $this->url;
    }
}
