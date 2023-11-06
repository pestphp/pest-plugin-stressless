<?php

declare(strict_types=1);

namespace Pest\Stressless;

use Pest\Contracts\Plugins\HandlesArguments;
use Pest\Plugins\Concerns\HandleArguments;
use Pest\Support\View;

/**
 * @internal
 */
final class Plugin implements HandlesArguments
{
    use HandleArguments;

    /**
     * Creates a new instance of the plugin.
     */
    public function __construct()
    {
        // ..
    }

    /**
     * {@inheritdoc}
     */
    public function handleArguments(array $arguments): array
    {
        if (! array_key_exists(1, $arguments)) {
            return $arguments;
        }
        if ($arguments[1] !== 'stress') {
            return $arguments;
        }
        if (! array_key_exists(2, $arguments)) {
            View::render('components.badge', [
                'type' => 'ERROR',
                'content' => 'Missing stress domain. Please provide a domain to stress.',
            ]);

            exit(0);
        }

        $domain = $arguments[2];

        $run = stress($domain);

        foreach ($arguments as $argument) {
            if (str_starts_with($argument, '--duration=')) {
                $run->duration((int) str_replace('--duration=', '', $argument));
            }
        }

        foreach ($arguments as $argument) {
            if (str_starts_with($argument, '--concurrency=')) {
                $run->concurrently((int) str_replace('--concurrency=', '', $argument));
            }
        }

        $run->dd();
    }
}
