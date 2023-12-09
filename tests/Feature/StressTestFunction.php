<?php

declare(strict_types=1);

use function Pest\Stressless\stress;

stress('the homepage', function (): void {
    stress('example.com');

    expect(test()->groups()[0])->toBe('stress');
});
