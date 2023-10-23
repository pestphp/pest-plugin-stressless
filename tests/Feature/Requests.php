<?php

declare(strict_types=1);

use function Pest\Stressless\{stress};

test('stress', function () {
    $result = stress('http://127.0.0.1:8000')
        ->with(2)->concurrentRequests()
        ->for(2)->seconds();

    expect($result->totalRequests())
        ->toBeInt()
        ->toBeGreaterThan(0)
        ->toBe($result->successfulRequests())
        ->and($result->failedRequests)->toBe(0);
});
