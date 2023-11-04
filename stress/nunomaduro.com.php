<?php

declare(strict_types=1);

use function Pest\Stressless\stress;

stress('nunomaduro.com')
    ->with(2)->concurrentRequests()
    ->for(3)->seconds();
