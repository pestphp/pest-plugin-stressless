<?php

use Pest\Stressless\Binary;

it('infers the path from the environment', function () {
    $binary = Binary::k6();

    expect((string) $binary)->toBe(realpath(__DIR__.'/../../bin/k6-macos-arm64'));
});
