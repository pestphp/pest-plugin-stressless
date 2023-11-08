<?php

declare(strict_types=1);

use Pest\Stressless\K6;

it('infers the path from the environment on mac OS', function (): void {
    $binary = K6::make();

    $arch = str_contains(php_uname('m'), 'arm') ? 'arm64' : 'amd64';

    expect((string) $binary)->toBe(realpath(__DIR__.'/../../bin/k6-'.k6::K6_VERSION.'-macos-'.$arch.'/k6'));
})->skipOnLinux()->skipOnWindows();

it('infers the path from the environment on Linux', function (): void {
    $binary = K6::make();

    $arch = str_contains(php_uname('m'), 'arm') ? 'arm64' : 'amd64';

    expect((string) $binary)->toBe(realpath(__DIR__.'/../../bin/k6-'.k6::K6_VERSION.'-linux-'.$arch.'/k6'));
})->skipOnMac()->skipOnWindows();

it('infers the path from the environment on Windows', function (): void {
    $binary = K6::make();

    $arch = 'amd64'; // Always amd64 on windows

    expect((string) $binary)->toBe(realpath(__DIR__.'/../../bin/k6-'.k6::K6_VERSION.'-windows-'.$arch.'/k6.exe'));
})->skipOnMac()->skipOnLinux();
