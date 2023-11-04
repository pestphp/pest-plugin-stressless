<?php

declare(strict_types=1);

namespace Pest\Stressless;

use Pest\Stressless\Fluent\WithOptions;
use Pest\Stressless\ValueObjects\Result;
use Pest\Stressless\ValueObjects\Url;
use Pest\TestSuite;

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
        return $this->result = (new Run(
            new Url($this->url),
            $this->options,
            $this->verbose,
        ))->start();
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

    public function __destruct()
    {
        if (! $this->result instanceof Result && TestSuite::getInstance()->test === null) {
            $this->dd();
        }
    }
}
