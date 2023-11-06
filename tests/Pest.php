<?php

declare(strict_types=1);

use function Pest\Stressless\stress;

uses()->beforeEach(function (): void {
    $this->stress = $_SERVER['stress'] ??= stress('example.com')
        ->with(2)->concurrentRequests()
        ->for(1)->second();

    expect($this->stress->requests->failed->count())->toBe(0);
})->in(__DIR__);
