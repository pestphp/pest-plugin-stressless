<?php

declare(strict_types=1);

use function Pest\Stressless\stress;

it('can dump the result', function (): void {
    $result = stress('example.com')
        ->with(2)->concurrentRequests()
        ->for(1)->second()
        ->dump();

    expect($result->requests()->duration()->avg)->toBeGreaterThan(0);
});
