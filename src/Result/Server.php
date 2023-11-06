<?php

declare(strict_types=1);

namespace Pest\Stressless\Result;

use Pest\Stressless\Result;

/**
 * @internal
 *
 * @property-read Duration $duration
 */
final readonly class Server
{
    /**
     * Creates a new requests instance.
     */
    public function __construct(private Result $result)
    {
        //
    }

    /**
     * Returns the details of the requests server duration.
     */
    public function duration(): Duration
    {
        return new Duration($this->result->toArray()['metrics']['http_req_waiting']['values']);
    }

    /**
     * Proxies the properties to methods.
     */
    public function __get(string $name): mixed
    {
        return $this->{$name}(); // @phpstan-ignore-line
    }
}
