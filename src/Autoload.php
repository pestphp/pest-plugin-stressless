<?php

declare(strict_types=1);

namespace Pest\Stressless;

use Closure;
use Pest\PendingCalls\TestCall;

/**
 * If passed two parameters, this function creates a test
 * with the `stress` group. If only passed one parameter,
 * it will perform a stress test on the given URL.
 */
function stress(string $description, ?Closure $test = null): Factory|TestCall
{
    return func_num_args() > 1
        ? test($description, func_get_arg(1))->group('stress')
        : visit($description);
}

/**
 * Performs a stress test on the given URL.
 */
function visit(string $url): Factory
{
    return Factory::make($url);
}
