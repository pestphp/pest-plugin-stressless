<?php

declare(strict_types=1);

namespace Pest\Stressless;

use Symfony\Component\Process\Process;

/**
 * @internal
 */
final readonly class Run
{
    /**
     * Creates a new run instance.
     *
     * @param  array<string, mixed>  $options
     */
    public function __construct(private string $url, private array $options)
    {
        //
    }

    /**
     * Processes the run.
     */
    public function start(): Result
    {
        $basePath = dirname(__DIR__);

        $url = is_int(preg_match('/^https?:\/\//', $this->url)) ? $this->url : 'https://'.$this->url;

        $process = new Process([
            'k6', 'run', 'run.js',
        ], $basePath.'/bin', [
            'PEST_STRESS_TEST_OPTIONS' => json_encode($this->options, JSON_THROW_ON_ERROR),
            'PEST_STRESS_TEST_URL' => $url,
        ]);

        $process->run();
        if (! $process->isSuccessful()) {
            dd($process->getErrorOutput());
        }

        $summary = file_get_contents($basePath.'/bin/summary.json');
        assert(is_string($summary));

        $metrics = json_decode($summary, true, 512, JSON_THROW_ON_ERROR);
        assert(is_array($metrics));

        return new Result($metrics); // @phpstan-ignore-line
    }
}
