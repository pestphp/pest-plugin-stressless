<?php

declare(strict_types=1);

it('can dump the result', function (): void {
    $result = $this->stress->dump();

    expect($result->requests()->duration()->avg)->toBeGreaterThan(0);
});
