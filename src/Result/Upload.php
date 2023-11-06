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
final readonly class Upload
{
    /**
     * Creates a new requests instance.
     */
    public function __construct(private Result $result)
    {
        //
    }

    /**
     * Returns the details of the requests upload data.
     */
    public function data(): Rate
    {
        return new Rate($this->result->toArray()['metrics']['data_sent']['values']);
    }

    /**
     * Returns the details of the requests upload duration.
     */
    public function duration(): Duration
    {
        return new Duration($this->result->toArray()['metrics']['http_req_sending']['values']);
    }
}
