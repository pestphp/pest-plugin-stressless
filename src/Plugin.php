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
        if ($this->hasArgument('--stress', $arguments)) {
            return $this->pushArgument('--group=stress', $this->popArgument('--stress', $arguments));
        }

        if ($this->hasArgument('--exclude-stress', $arguments)) {
            return $this->pushArgument('--exclude-group=stress', $this->popArgument('--exclude-stress', $arguments));
        }

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

            if (str_starts_with($argument, '--concurrency=')) {
                $run->concurrently((int) str_replace('--concurrency=', '', $argument));
            }

            if ($argument === '--delete') {
                $run->delete();
            }

            if ($argument === '--get') {
                $run->get();
            }

            if ($argument === '--head') {
                $run->head();
            }

            if (str_starts_with($argument, '--options=')) {
                $run->options($this->extractPayload('options', $argument));
            } elseif ($argument === '--options') {
                $run->options();
            }

            if (str_starts_with($argument, '--patch=')) {
                $run->patch($this->extractPayload('patch', $argument));
            } elseif ($argument === '--patch') {
                $run->patch();
            }

            if (str_starts_with($argument, '--put=')) {
                $run->put($this->extractPayload('put', $argument));
            } elseif ($argument === '--put') {
                $run->put();
            }

            if (str_starts_with($argument, '--post=')) {
                $run->post($this->extractPayload('post', $argument));
            }
        }

        $run->dd();
    }

    /**
     * Extracts the payload from the argument.
     *
     * @return array<string, mixed>
     */
    private function extractPayload(string $method, string $argument): array
    {
        try {
            return (array) json_decode(str_replace("--{$method}=", '', $argument),
                true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            View::render('components.badge', [
                'type' => 'ERROR',
                'content' => 'Invalid JSON payload. Please provide a valid JSON payload. '.
                    "Example: --{$method}='{\"name\": \"Nuno\"}'",
            ]);

            exit(0);
        }
    }
}
