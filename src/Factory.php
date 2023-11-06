<?php

declare(strict_types=1);

namespace Pest\Stressless;

use Pest\TestSuite;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @mixin Result
 */
final class Factory
{
    /**
     * Weather or not the run should be verbose.
     */
    private bool $verbose = false;

    /**
     * The number of concurrent requests.
     */
    private int $concurrency = 1;

    /**
     * The duration of the run in seconds.
     */
    private int $duration = 5;

    /**
     * Weather or not the factory is running.
     */
    private bool $running = false;

    /**
     * The computed result, if any.
     */
    private ?Result $result = null;

    /**
     * Creates a new instance of the run factory.
     *
     * @param  array{stages: array<int, array{duration: string, target: int}>}  $options
     */
    private function __construct(private readonly string $url, private array $options)
    {
        //
    }

    /**
     * Creates a new instance of the run factory.
     */
    public static function make(string $url): self
    {
        return new self($url, ['stages' => []]);
    }

    /**
     * Specifies that run should run for the given number of seconds.
     */
    public function duration(int $seconds): self
    {
        assert($seconds > 0, 'The duration must be greater than 0 seconds.');

        $this->duration = $seconds;

        return $this;
    }

    /**
     * Specifies that run should run with the given number of concurrent requests.
     */
    public function concurrency(int $requests): self
    {
        assert($requests > 0, 'The concurrency must be greater than 0 requests.');

        $this->concurrency = $requests;

        return $this;
    }

    /**
     * Specifies that run should run with the given number of concurrent requests.
     */
    public function concurrently(int $requests): self
    {
        return $this->concurrency($requests);
    }

    /**
     * Specifies that the stage should run for the given duration.
     */
    public function for(int $duration): DurationOptions
    {
        return new DurationOptions($this, $duration);
    }

    /**
     * Creates a new run instance.
     */
    public function run(): Result
    {
        $this->options['stages'] = [[
            'duration' => sprintf('%ds', $this->duration),
            'target' => $this->concurrency,
        ]];

        $this->options['throw'] = true;

        $this->running = true;

        return $this->result ??= ((new Run(
            new Url($this->url),
            $this->options,
            $this->verbose,
        ))->start());
    }

    /**
     * Specifies that the run should be verbose, and then exits.
     */
    public function dd(): never
    {
        $this->dump();

        exit(0);
    }

    /**
     * Specifies that the run should be verbose.
     */
    public function dump(): self
    {
        $this->verbosely();

        $this->run();

        return $this;
    }

    /**
     * Specifies that the run should be verbose.
     */
    public function verbosely(): self
    {
        $this->verbose = true;

        return $this;
    }

    /**
     * Forwards calls to the run result.
     *
     * @param  array<int, mixed>  $arguments
     */
    public function __call(string $name, array $arguments): mixed
    {
        if (! $this->result instanceof Result) {
            $this->run();
        }

        return $this->result->{$name}(...$arguments); // @phpstan-ignore-line
    }

    /**
     * Forwards property access to the run result.
     */
    public function __get(string $name): mixed
    {
        return $this->{$name}(); // @phpstan-ignore-line
    }

    /**
     * Destructs the run factory.
     */
    public function __destruct()
    {
        if ($this->result instanceof Result) {
            return;
        }

        if (TestSuite::getInstance()->test instanceof TestCase) {
            return;
        }

        if ($this->running) {
            return;
        }

        $this->dd();
    }
}
