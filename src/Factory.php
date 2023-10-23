<?php

declare(strict_types=1);

namespace Pest\Stressless;

use Pest\Stressless\Fluent\WithOptions;

/**
 * @internal
 *
 * @mixin Result
 */
final class Factory
{
    /**
     * The computed result, if any.
     */
    private ?Result $result = null;

    /**
     * Creates a new instance of the run factory.
     *
     * @param  array{stages: array<array{duration: string, target: int}>}  $options
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
     * Specifies that run should run with the given number of something to be determined.
     */
    public function with(int $number): WithOptions
    {
        return new WithOptions($this, $number);
    }

    /**
     * Specifies that run should run with the given number of something to be determined.
     */
    public function then(int $with): WithOptions
    {
        return new WithOptions($this, $with);
    }

    /**
     * Specifies that the stress test should make the given number of requests concurrently for the given duration in seconds.
     */
    public function stage(int $requests, int $seconds): self
    {
        $this->options['stages'][] = [
            'duration' => "{$seconds}s",
            'target' => $requests,
        ];

        return $this;
    }

    /**
     * Creates a new run instance.
     */
    public function run(): Result
    {
        return $this->result = (new Run($this->url, $this->options))->start();
    }

    /**
     * Forwards calls to the run result.
     *
     * @param  array<int, mixed>  $arguments
     */
    public function __call(string $name, array $arguments): mixed
    {
        if (! $this->result instanceof \Pest\Stressless\Result) {
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
