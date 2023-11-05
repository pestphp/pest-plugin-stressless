<?php

declare(strict_types=1);

use function Pest\Stressless\stress;

it('may pass', function (): void {
    $result = stress('example.com')
        ->with(2)->concurrentRequests()
        ->for(1)->second();

    expect($result->failedRequests())->toBe(0);
});

it('may fail', function (): void {
    $result = stress('dummy-example.com')
        ->with(2)->concurrentRequests()
        ->for(1)->second();

    expect($result->failedRequests())
        ->toBeGreaterThan(2)
        ->toBe($result->requests());
});
