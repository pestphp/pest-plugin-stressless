<?php

declare(strict_types=1);

namespace Pest\Stressless\Result;

use Pest\Stressless\Result;

/**
 * @internal
 *
 * @property-read Duration $duration
 * @property-read Rate     $data
 */
final readonly class Download
{
    /**
     * Creates a new requests instance.
     */
    public function __construct(private Result $result)
    {
        //
    }

    /**
     * Returns the details of the requests download data.
     */
    public function data(): Rate
    {
        return new Rate($this->result->toArray()['metrics']['data_received']['values']);
    }

    /**
     * Returns the details of the requests download duration.
     */
    public function duration(): Duration
    {
        return new Duration($this->result->toArray()['metrics']['http_req_receiving']['values']);
    }
}
