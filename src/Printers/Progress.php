<?php

declare(strict_types=1);

namespace Pest\Stressless\Printers;

use JsonException;
use Pest\Stressless\Session;
use Pest\Stressless\Url;
use Symfony\Component\Process\Process;

use function Termwind\render;
use function Termwind\terminal;

/**
 * @internal
 */
final readonly class Progress
{
    /**
     * Creates a new progress instance.
     */
    public function __construct(private Process $process, private Session $session, private Url $url)
    {
        //
    }

    /**
     * Tails the progress file.
     */
    public function tail(): void
    {
        $domain = $this->url->domain();

        $concurrency = $this->session->concurrency();
        $duration = $this->session->duration();

        $options = 'for '.$duration.' second';

        if ($duration > 1) {
            $options .= 's';
        }

        if ($concurrency > 1) {
            $options = $concurrency.' concurrent requests '.$options.' ';
        }

        render(<<<HTML
            <div class="flex mx-2 mt-1 max-w-150 space-x-1 text-gray">
                <span>Stress testing <span class="text-cyan font-bold">$domain</span></span>
                <span class="flex-1 content-repeat-[―]"></span>
                <span>$options</span>
            </div>
        HTML);

        sleep(1);

        $tail = new Process(['tail', '-f', $this->session->progressPath()]);

        $tail
            ->setTty(false)
            ->setTimeout(null)
            ->start();

        /** @var array<int, array{data: array{time: string, value: float}}> $points */
        $points = [];
        $buffer = '';
        $currentTime = '';
        $lastTime = '';

        while ($this->process->isRunning()) {
            $this->fetch($tail, $points, $buffer, $currentTime, $lastTime);
        }

        $this->fetch($tail, $points, $buffer, $currentTime, $lastTime);
    }

    /**
     * Fetches the tail output.
     *
     * @param  array<int, array{data: array{time: string, value: float}}>  $points
     */
    private function fetch(Process $tail, array &$points, string &$buffer, string &$currentTime, string &$lastTime): void
    {
        $output = trim($tail->getIncrementalOutput());

        if ($output === '') {
            return;
        }

        $output = $buffer.$output;
        $buffer = '';

        $lines = explode("\n", $output);

        foreach ($lines as $line) {
            if (str_starts_with($line, '{"metric":"http_req_duration","type":"Point"')) {
                try {
                    /** @var array{data: array{time: string, value: float}}|null $point */
                    $point = json_decode($line, true, 512, JSON_THROW_ON_ERROR);
                    assert(is_array($point));

                    $currentTime = substr($point['data']['time'], 0, 19);
                    if ($lastTime !== $currentTime) {
                        $this->printCurrentPoints($points);

                        $lastTime = $currentTime;
                    }

                    $points[] = $point;
                } catch (JsonException) {
                    $buffer .= $line;
                }
            }
        }

        usleep(10000); // 10ms

    }

    /**
     * Prints the current points.
     *
     * @param  array<array{data: array{time: string, value: float}}>  $points
     */
    private function printCurrentPoints(array $points): void
    {
        static $maxResponseTime;

        if ($points !== []) {
            $values = array_map(fn ($point): float => $point['data']['value'], $points);
            $median = $this->median($values);

            $time = substr($points[count($points) - 1]['data']['time'], 11, 8);

            $width = max(0, terminal()->width());
            $width = $width - 4 - strlen($time);

            if ($maxResponseTime === null) {
                $maxResponseTime = max($median * 3, 1000);
            }

            $greenDots = (int) (($median * $width) / $maxResponseTime);

            $greenDots = min($greenDots, min(150, terminal()->width()) - 23);
            $greenDots = str_repeat('▉', $greenDots);

            $median = sprintf('%4.2f', $median);

            render(<<<HTML
                <div class="flex justify-between mx-2 max-w-150 text-gray">
                    <span>
                        <span>{$time}</span>
                        <span class="ml-1">{$greenDots}</span>
                    </span>
                    <span class="ml-1">{$median}ms</span>
                </div>
            HTML);
        }
    }

    /**
     * Calculates the median.
     *
     * @param  array<int, float>  $values
     */
    private function median(array $values): float
    {
        $count = count($values);

        if ($count === 0) {
            return 0.0;
        }

        sort($values);

        $middle = (int) floor(($count - 1) / 2);

        if ($count % 2 === 1) {
            return $values[$middle];
        }

        return ($values[$middle] + $values[$middle + 1]) / 2.0;
    }
}
