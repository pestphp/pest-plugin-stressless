<?php

declare(strict_types=1);

namespace Pest\Stressless;

use Pest\Exceptions\ShouldNotHappen;
use Pest\Stressless\ResultPrinters\Blocks;
use Pest\Stressless\ValueObjects\Binary;
use Pest\Stressless\ValueObjects\Result;
use Pest\Stressless\ValueObjects\Url;
use RuntimeException;
use Symfony\Component\Process\Process;

use function Termwind\render;
use function Termwind\terminal;

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
    public function __construct(private Url $url, private array $options, private bool $verbose)
    {
        //
    }

    /**
     * Processes the run.
     */
    public function start(): Result
    {
        $session = new Session(
            $basePath = dirname(__DIR__),
            uniqid('pest', true),
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
            $this->tailProgress($process, $session->progressPath());
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

        $result = new Result($metrics);

        if ($this->verbose) {
            $blocks = new Blocks();

            $blocks->print($result);
        }

        $session->clean();

        return $result;
    }

    private function tailProgress(Process $process, string $progressPath): void
    {
        $date = date('H:i:s');
        $url = str_starts_with($this->url, 'http') ? $this->url : 'https://'.$this->url;
        $url = explode('//', (string) $url)[1];

        render(<<<HTML
            <div class="flex mx-2">
                <span class="text-gray">$date</span>
                <span class="flex-1"></span>
                <span class="text-gray">Running stress test on <span class="text-blue">$url</span></span>
            </div>
        HTML);

        sleep(1);

        $tail = new Process(['tail', '-f', $progressPath]);

        $tail
            ->setTty(false)
            ->setTimeout(null)
            ->start();

        $points = [];

        $buffer = '';
        $lastTime = null;
        while ($process->isRunning()) {
            $output = $tail->getIncrementalOutput();

            if (empty($output)) {
                continue;
            }

            $output = $buffer.$output;
            $buffer = '';

            $lines = explode("\n", $output);

            foreach ($lines as $line) {
                if (str_starts_with($line, '{"metric":"http_req_duration","type":"Point"')) {
                    $decodedLine = json_decode($line, true, 512, JSON_THROW_ON_ERROR);

                    if (is_array($decodedLine)) {
                        $currentTime = substr((string) $decodedLine['data']['time'], 0, 19);
                        if ($lastTime !== $currentTime) {
                            $this->printCurrentPoints($points);
                            $points = [];

                            $lastTime = $currentTime;
                        }

                        $points[] = $decodedLine;
                    } else {
                        $buffer .= $line;
                    }
                }
            }

            usleep(100000); // 100ms
        }
    }

    private function printCurrentPoints(array $points): void
    {
        static $maxResponseTime;

        if ($points !== []) {
            $average = array_sum(array_map(fn ($point) => $point['data']['value'], $points)) / count($points);
            $average = round($average, 2);

            // only time
            $time = substr((string) $points[0]['data']['time'], 11, 8);

            $width = max(0, terminal()->width());
            $width = $width - 4 - strlen($time);

            if ($maxResponseTime === null) {
                $maxResponseTime = max($average * 3, 1000);
            }

            $greenDots = (int) (($average * $width) / $maxResponseTime);

            $greenDots = str_repeat('█', $greenDots);

            render(<<<HTML
                <div class="flex mx-2">
                    <span class="text-gray">
                        <span>{$time}│</span>
                        <span class="">$greenDots</span>
                    </span>
                    <span class="flex-1"></span>
                    <span class="text-gray ml-1">{$average}ms</span>
                </div>
            HTML);
        }
    }

    /**
     * Destroys the run instance.
     */
    public function __destruct()
    {
        //
    }
}
