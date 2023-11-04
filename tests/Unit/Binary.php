<?php

declare(strict_types=1);

use Pest\Stressless\ValueObjects\Binary;

it('infers the path from the environment', function (): void {
    $binary = Binary::k6();

    expect((string) $binary)->toBe(realpath(__DIR__.'/../../bin/k6-macos-arm64'));
});
