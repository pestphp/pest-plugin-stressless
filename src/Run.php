<?php

declare(strict_types=1);

namespace Pest\Stressless;

use Pest\Exceptions\ShouldNotHappen;
use Pest\Stressless\Printers\Detail;
use Pest\Stressless\Printers\Progress;
use RuntimeException;
use Symfony\Component\Process\Process;

use function Termwind\render;

/**
 * @internal
 */
final class Run
{
    /**
     * The sessions instance, if any.
     */
    private ?Session $session = null;

    /**
     * Creates a new run instance.
     *
     * @param  array{stages: array{0: array{duration: string, target: int}}}  $options
     */
    public function __construct(
        readonly private Url $url,
        readonly private array $options,
        readonly private bool $verbose
    ) {
        //
    }

    /**
     * Processes the run.
     */
    public function start(): Result
    {
        $concurrency = $this->options['stages'][0]['target'];
        $duration = (int) $this->options['stages'][0]['duration'];

        $this->session = new Session(
            $basePath = dirname(__DIR__),
            uniqid('pest', true),
            $concurrency,
            $duration,
        );

        if (! K6::exists()) {
            render(<<<'HTML'
                <div class="mx-2 mt-1">
                    <span class="bg-cyan font-bold px-1 mr-1">INFO</span>
                    <span class="font-bold">Hang tight — we're setting things up for your first stress test!</span>
                </div>
            HTML);

            K6::download();
        }

        $process = new Process([
            K6::make(), 'run', 'run.js', '--out', "json={$this->session->progressPath()}",
        ], $basePath.'/bin', [
            'PEST_STRESS_TEST_OPTIONS' => json_encode($this->options, JSON_THROW_ON_ERROR),
            'PEST_STRESS_TEST_URL' => $this->url,
            'PEST_STRESS_TEST_SUMMARY_PATH' => $this->session->summaryPath(),
        ]);

        $process->start();

        if ($this->verbose) {
            (new Progress($process, $this->session, $this->url))->tail();
        }

        $process->wait();

        if (! $process->isSuccessful()) {
            throw new ShouldNotHappen(
                new RuntimeException(sprintf('The underlying K6 process failed with the following error: %s', $process->getErrorOutput())),
            );
        }

        $summary = file_get_contents($this->session->summaryPath());
        assert(is_string($summary));

        $metrics = json_decode($summary, true, 512, JSON_THROW_ON_ERROR);
        assert(is_array($metrics));

        $result = new Result($this->url, $metrics); // @phpstan-ignore-line

        if ($this->verbose) {
            $detail = new Detail();

            $detail->print($result);
        }

        $this->session->clean();

        $this->session = null;

        return $result;
    }

    /**
     * Destroys the factory instdance instance.
     */
    public function __destruct()
    {
        $this->session?->clean();
    }
}
