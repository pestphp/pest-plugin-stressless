<?php

declare(strict_types=1);

namespace Pest\Stressless;

function stress(string $url): Factory
{
    return Factory::make($url);
}
