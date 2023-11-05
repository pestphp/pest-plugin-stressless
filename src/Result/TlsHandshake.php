<?php

declare(strict_types=1);

namespace Pest\Stressless\Result;

use Pest\Stressless\ValueObjects\Result;

/**
 * @internal
 *
 * @property-read Duration $duration
 */
final readonly class TlsHandshake
{
    /**
     * Creates a new requests instance.
     */
    public function __construct(private Result $result)
    {
        //
    }

    /**
     * Returns the details of the requests TLS Handshake duration.
     */
    public function duration(): Duration
    {
        return new Duration($this->result->toArray()['metrics']['http_req_tls_handshaking']['values']);
    }
}
