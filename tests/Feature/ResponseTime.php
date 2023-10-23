<?php

declare(strict_types=1);

use function Pest\Stressless\{stress};

test('stress', function () {
    $result = stress('http://127.0.0.1:8000')
        ->with(10)->concurrentRequests()
        ->for(2)->seconds();

    expect($result->averageResponseTime())->toBeLessThan(1000);
});
