<?php

declare(strict_types=1);

use Pest\Stressless\Result;

test('duration', function (): void {
    /** @var Result $result */
    $result = $this->stress->run();

    $duration = $result->testRun()->duration();

    expect($duration)
        ->toBeFloat()
        ->toBeGreaterThan(0.0);
});

test('concurrency', function (): void {
    /** @var Result $result */
    $result = $this->stress->run();

    $concurrency = $result->testRun()->concurrency();

    expect($concurrency)
        ->toBeInt()
        ->toBe(2);
});
