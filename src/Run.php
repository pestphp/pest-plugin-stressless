<?php

declare(strict_types=1);

namespace Pest\Stressless;

use Pest\Exceptions\ShouldNotHappen;
use Pest\Stressless\Printers\Detail;
use Pest\Stressless\Printers\Progress;
use RuntimeException;
use Symfony\Component\Process\Process;

/**
 * @internal
 */
final readonly class Run
{
    /**
     * Creates a new run instance.
     *
     * @param  array{stages: array{0: array{duration: string, target: int}}}  $options
     */
    public function __construct(private Url $url, private array $options, private bool $verbose)
    {
        //
    }

    /**
     * Processes the run.
     */
    public function start(): Result
    {
        $concurrency = $this->options['stages'][0]['target'];
        $duration = (int) $this->options['stages'][0]['duration'];

        $session = new Session(
            $basePath = dirname(__DIR__),
            uniqid('pest', true),
            $concurrency,
            $duration,
        );

        $process = new Process([
            Binary::k6(), 'run', 'run.js', '--out', "json={$session->progressPath()}",
        ], $basePath.'/bin', [
            'PEST_STRESS_TEST_OPTIONS' => json_encode($this->options, JSON_THROW_ON_ERROR),
            'PEST_STRESS_TEST_URL' => $this->url,
            'PEST_STRESS_TEST_SUMMARY_PATH' => $session->summaryPath(),
        ]);

        $process->start();

        if ($this->verbose) {
            (new Progress($process, $session, $this->url))->tail();
        }

        $process->wait();

        if (! $process->isSuccessful()) {
            throw new ShouldNotHappen(
                new RuntimeException(sprintf('The underlying K6 process failed with the following error: %s', $process->getErrorOutput())),
            );
        }

        $summary = file_get_contents($session->summaryPath());
        assert(is_string($summary));

        $metrics = json_decode($summary, true, 512, JSON_THROW_ON_ERROR);
        assert(is_array($metrics));

        $result = new Result($this->url, $metrics); // @phpstan-ignore-line

        if ($this->verbose) {
            $detail = new Detail();

            $detail->print($result);
        }

        $session->clean();

        return $result;
    }
}
