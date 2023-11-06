<?php

declare(strict_types=1);

use Pest\Stressless\Binary;

it('infers the path from the environment on mac OS', function (): void {
    $binary = Binary::k6();

    $arch = str_contains(php_uname('m'), 'arm') ? 'arm64' : 'amd64';

    expect((string) $binary)->toBe(realpath(__DIR__.'/../../bin/k6-macos-'.$arch));
})->skipOnLinux()->skipOnWindows();

it('infers the path from the environment on Linux', function (): void {
    $binary = Binary::k6();

    $arch = str_contains(php_uname('m'), 'arm') ? 'arm64' : 'amd64';

    expect((string) $binary)->toBe(realpath(__DIR__.'/../../bin/k6-linux-'.$arch));
})->skipOnMac()->skipOnWindows();
