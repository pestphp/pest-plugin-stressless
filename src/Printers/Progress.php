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
            <div class="flex mx-2 max-w-150 space-x-1 text-gray">
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
        $lastTime = null;
        while ($this->process->isRunning()) {
            $output = trim($tail->getIncrementalOutput());

            if ($output === '') {
                continue;
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
                            $points = [];

                            $lastTime = $currentTime;
                        }

                        $points[] = $point;
                    } catch (JsonException) {
                        $buffer .= $line;
                    }
                }
            }

            usleep(100000); // 100ms
        }
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
            $average = array_sum(array_map(fn ($point): float => $point['data']['value'], $points)) / count($points);

            $time = substr($points[0]['data']['time'], 11, 8);

            $width = max(0, terminal()->width());
            $width = $width - 4 - strlen($time);

            if ($maxResponseTime === null) {
                $maxResponseTime = max($average * 3, 1000);
            }

            $greenDots = (int) (($average * $width) / $maxResponseTime);

            $greenDots = str_repeat('▉', $greenDots);

            $average = sprintf('%4.2f', $average);

            render(<<<HTML
                <div class="flex justify-between mx-2 max-w-150 text-gray">
                    <span>
                        <span>{$time}</span>
                        <span class="ml-1">{$greenDots}</span>
                    </span>
                    <span class="ml-1">{$average}ms</span>
                </div>
            HTML);
        }
    }
}
