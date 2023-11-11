<?php

declare(strict_types=1);

namespace Pest\Stressless;

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
     * The HTTP method to use.
     */
    private string $method = 'get';

    /**
     * The payload to send.
     * @var array<string, mixed>
     */
    private array $payload = [];

    /**
     * The computed result, if any.
     */
    private ?Result $result = null;

    /**
     * Creates a new instance of the run factory.
     */
    private function __construct(private readonly string $url)
    {
        //
    }

    /**
     * Creates a new instance of the run factory.
     */
    public static function make(string $url): self
    {
        return new self($url);
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
     * Specifies that the test should be run using the given HTTP method.
     */
    public function method(string $method): self
    {
        $method = strtolower($method);

        assert(in_array($method, ['get', 'post'], true), 'The method must be one of: get, post');

        $this->method = $method;

        return $this;
    }

    /**
     * Specifies the payload to send for the test, if any.
     * @param  array<string, mixed>  $payload
     */
    public function payload(array $payload): self
    {
        $this->payload = $payload;

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
        if ($this->result instanceof Result) {
            return $this->result;
        }

        return $this->result = ((new Run(
            new Url($this->url),
            [
                'vus' => $this->concurrency,
                'duration' => sprintf('%ds', $this->duration),
                'method' => $this->method,
                'payload' => $this->payload,
                'throw' => true,
            ],
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
}
