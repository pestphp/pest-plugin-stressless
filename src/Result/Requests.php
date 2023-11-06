<?php

declare(strict_types=1);

namespace Pest\Stressless\Result;

use Pest\Stressless\Result;

/**
 * @internal
 *
 * @property-read Duration $duration
 * @property-read Download $download
 * @property-read Rate $failed
 * @property-read Server $server
 * @property-read Upload $upload
 * @property-read DnsLookup $dnsLookup
 * @property-read float $rate
 * @property-read int $count
 * @property-read TlsHandshake $tlsHandshake
 */
final readonly class Requests
{
    /**
     * Creates a new requests instance.
     */
    public function __construct(private Result $result)
    {
        //
    }

    /**
     * Returns the details of the requests duration.
     */
    public function duration(): Duration
    {
        return new Duration($this->result->toArray()['metrics']['http_req_duration']['values']);
    }

    /**
     * Returns the details of the requests download.
     */
    public function download(): Download
    {
        return new Download($this->result);
    }

    /**
     * Returns the details of the failed requests.
     */
    public function failed(): Rate
    {
        return new Rate($this->result->toArray()['metrics']['http_req_failed']['values']);
    }

    /**
     * Returns the details of the requests server.
     */
    public function server(): Server
    {
        return new Server($this->result);
    }

    /**
     * Returns the details of the requests upload.
     */
    public function upload(): Upload
    {
        return new Upload($this->result);
    }

    /**
     * Returns the details of the requests DNS lookup.
     */
    public function dnsLookup(): DnsLookup
    {
        return new DnsLookup($this->result);
    }

    /**
     * Returns the rate.
     */
    public function rate(): float
    {
        return $this->result->toArray()['metrics']['http_reqs']['values']['rate'];
    }

    /**
     * Returns the count.
     */
    public function count(): int
    {
        return $this->result->toArray()['metrics']['http_reqs']['values']['count'];
    }

    /**
     * Returns the details of the requests TLS handshake duration.
     */
    public function tlsHandshake(): TlsHandshake
    {
        return new TlsHandshake($this->result);
    }

    /**
     * Proxies the properties to methods.
     */
    public function __get(string $name): mixed
    {
        return $this->{$name}(); // @phpstan-ignore-line
    }
}
