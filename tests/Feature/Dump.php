<?php

declare(strict_types=1);

use function Pest\Stressless\stress;

it('can dump the result', function (): void {
    $result = $this->stress->dump();

    expect($result->requests()->duration()->avg)->toBeGreaterThan(0);
});
