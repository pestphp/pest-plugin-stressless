<?php

declare(strict_types=1);

use Pest\Stressless\ValueObjects\Result;

test('duration', function (): void {
    /** @var Result $result */
    $result = $this->stress->run();

    $duration = $result->requests()->duration();

    expect($duration->avg())
        ->toBeFloat()
        ->toBeGreaterThan(0.0)
        ->and($duration->min())
        ->toBeFloat()
        ->toBeGreaterThan(0.0)
        ->and($duration->med())
        ->toBeFloat()
        ->toBeGreaterThan(0.0)
        ->and($duration->max())
        ->toBeFloat()
        ->toBeGreaterThan(0.0)
        ->and($duration->p90())
        ->toBeFloat()
        ->toBeGreaterThan(0.0)
        ->and($duration->p95())
        ->toBeFloat()
        ->toBeGreaterThan(0.0);
});

test('download data', function (): void {
    /** @var Result $result */
    $result = $this->stress->run();

    $data = $result->requests()->download()->data();

    expect($data->count())
        ->toBeInt()
        ->toBeGreaterThan(0)
        ->and($data->rate())
        ->toBeFloat()
        ->toBeGreaterThan(0.0);
});

test('download duration', function (): void {
    /** @var Result $result */
    $result = $this->stress->run();

    $duration = $result->requests()->download()->duration();

    expect($duration->avg())
        ->toBeFloat()
        ->toBeGreaterThan(0.0)
        ->and($duration->min())
        ->toBeFloat()
        ->toBeGreaterThan(0.0)
        ->and($duration->med())
        ->toBeFloat()
        ->toBeGreaterThan(0.0)
        ->and($duration->max())
        ->toBeFloat()
        ->toBeGreaterThan(0.0)
        ->and($duration->p90())
        ->toBeFloat()
        ->toBeGreaterThan(0.0)
        ->and($duration->p95());
});

test('failed', function (): void {
    /** @var Result $result */
    $result = $this->stress->run();

    $failed = $result->requests()->failed();

    expect($failed->count())
        ->toBeInt()
        ->toBe(0)
        ->and($failed->rate())
        ->toBeFloat()
        ->toBe(0.0);
});

test('server duration', function (): void {
    /** @var Result $result */
    $result = $this->stress->run();

    $duration = $result->requests()->server()->duration();

    expect($duration->avg())
        ->toBeFloat()
        ->toBeGreaterThan(0.0)
        ->and($duration->min())
        ->toBeFloat()
        ->toBeGreaterThan(0.0)
        ->and($duration->med())
        ->toBeFloat()
        ->toBeGreaterThan(0.0)
        ->and($duration->max())
        ->toBeFloat()
        ->toBeGreaterThan(0.0)
        ->and($duration->p90())
        ->toBeFloat()
        ->toBeGreaterThan(0.0)
        ->and($duration->p95())
        ->toBeFloat()
        ->toBeGreaterThan(0.0);
});

test('upload data', function (): void {
    /** @var Result $result */
    $result = $this->stress->run();

    $data = $result->requests()->upload()->data();

    expect($data->count())
        ->toBeInt()
        ->toBeGreaterThan(0)
        ->and($data->rate())
        ->toBeFloat()
        ->toBeGreaterThan(0.0);
});

test('upload duration', function (): void {
    /** @var Result $result */
    $result = $this->stress->run();

    $duration = $result->requests()->upload()->duration();

    expect($duration->avg())
        ->toBeFloat()
        ->toBeGreaterThan(0.0)
        ->and($duration->min())
        ->toBeFloat()
        ->toBeGreaterThan(0.0)
        ->and($duration->med())
        ->toBeFloat()
        ->toBeGreaterThan(0.0)
        ->and($duration->max())
        ->toBeFloat()
        ->toBeGreaterThan(0.0)
        ->and($duration->p90())
        ->toBeFloat()
        ->toBeGreaterThan(0.0)
        ->and($duration->p95());
});

test('dns lookup duration', function (): void {
    /** @var Result $result */
    $result = $this->stress->run();

    $duration = $result->requests()->dnsLookup()->duration();

    expect($duration->avg())
        ->toBeFloat()
        ->and($duration->min())
        ->toBeFloat()
        ->and($duration->med())
        ->toBeFloat()
        ->and($duration->max())
        ->toBeFloat()
        ->and($duration->p90())
        ->toBeFloat()
        ->and($duration->p95());
});

test('rate', function (): void {
    /** @var Result $result */
    $result = $this->stress->run();

    $requests = $result->requests();

    expect($requests->rate())
        ->toBeFloat()
        ->toBeGreaterThan(0.0);
});

test('count', function (): void {
    /** @var Result $result */
    $result = $this->stress->run();

    $requests = $result->requests();

    expect($requests->count())
        ->toBeInt()
        ->toBeGreaterThan(0);
});

test('tls handshake duration', function (): void {
    /** @var Result $result */
    $result = $this->stress->run();

    $duration = $result->requests()->tlsHandshake()->duration();

    expect($duration->avg())
        ->toBeFloat()
        ->and($duration->min())
        ->toBeFloat()
        ->and($duration->med())
        ->toBeFloat()
        ->and($duration->max())
        ->toBeFloat()
        ->and($duration->p90())
        ->toBeFloat()
        ->and($duration->p95());
});
