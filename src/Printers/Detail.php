<?php

declare(strict_types=1);

namespace Pest\Stressless\Printers;

use Pest\Stressless\Result;

use function Termwind\render;

/**
 * @internal
 */
final readonly class Detail
{
    /**
     * Prints the blocks.
     */
    public function print(Result $result): void
    {
        render(<<<'HTML'
            <div class="flex mx-2 max-w-150">
                <span class="text-gray">Result</span>
                <span class="flex-1 ml-1 content-repeat-[―] text-gray"></span>
            </div>
        HTML);

        $this->overview($result);

        $color = $this->color($result->requests->dnsLookup->duration->avg, 20.0, 50.0, 100.0);
        $value = $this->ms($result->requests->dnsLookup->duration->avg);
        $this->twoColumnDetail('DNS Lookup Duration', "<span class=\"$color\">$value</span>");

        $color = $this->color($result->requests->tlsHandshake->duration->avg, 20.0, 50.0, 100.0);
        $value = $this->ms($result->requests->tlsHandshake->duration->avg);
        $this->twoColumnDetail('TLS Handshake Duration', "<span class=\"$color\">$value</span>");

        $color = $this->color($result->requests->duration->avg, 100.0, 300.0, 1000.0);
        $this->twoColumnDetail(
            'Request Duration',
            '<span class="'.$color.'">'.$this->ms($total = $result->requests->duration->avg).'</span>'
        );

        $color = $this->color($result->requests->upload->duration->avg, 50.0, 150.0, 250.0);
        $value = $result->requests->upload->duration->avg;
        $percentage = $value * 100.0 / $total;
        $percentage = sprintf('%4.1f', $percentage);
        $value = $this->ms($value);
        $this->twoColumnDetail(<<<HTML
            <span>— Upload</span>
            <span class="ml-1 text-gray">$percentage %</span>
        HTML, "<span class=\"$color\">$value</span>");

        $color = $this->color($result->requests->server->duration->avg, 50.0, 150.0, 400.0);
        $value = $result->requests->server->duration->avg;
        $percentage = $value * 100.0 / $total;
        $percentage = sprintf('%4.1f', $percentage);
        $value = $this->ms($value);

        $this->twoColumnDetail(<<<HTML
            <span>— TTFB</span>
            <span class="ml-1 text-gray">$percentage %</span>
        HTML, "<span class=\"$color\">$value</span>");

        $color = $this->color($result->requests->download->duration->avg, 100.0, 300.0, 1000.0);
        $value = $result->requests->download->duration->avg;
        $percentage = $value * 100.0 / $total;
        $percentage = sprintf('%4.1f', $percentage);
        $value = $this->ms($value);
        $this->twoColumnDetail(<<<HTML
            <span>— Download</span>
            <span class="ml-1 text-gray">$percentage %</span>
        HTML, "<span class=\"$color\">$value</span>");

        render(<<<'HTML'
            <div class="mx-2 max-w-150 text-right flex text-gray">
                <span></span>
                <span class="flex-1"></span>
                <span>
                    <span class="text-red">■</span>
                    <span class="mx-1">Critical</span>
                    <span class="text-orange ml-1">■</span>
                    <span class="mx-1">Poor</span>
                    <span class="text-yellow ml-1">■</span>
                    <span class="mx-1">Ok</span>
                    <span class="text-green ml-1">■</span>
                    <span class="ml-1">Excellent</span>
                </span>
            </div>);
        HTML);
    }

    /**
     * Prints the overview's detail.
     */
    private function overview(Result $result): void
    {
        $metrics = $result->toArray()['metrics'];

        $testRunDuration = $result->testRun()->duration();
        $testRunDuration = sprintf('%4.2f', $testRunDuration / 1000);

        $this->twoColumnDetail('Test Duration', "$testRunDuration s");

        $requestsTotal = $metrics['http_reqs']['values']['count'];
        $requestsRate = round($metrics['http_reqs']['values']['rate'], 2);
        $result->testRun()->concurrency();

        $this->twoColumnDetail('Requests Count', <<<HTML
            <span class="text-gray mr-1">$requestsRate reqs/second</span>
            <span>$requestsTotal requests</span>
        HTML);

        $successRate = (float) ($metrics['http_req_failed']['values']['fails'] * 100 / $metrics['http_reqs']['values']['count']);
        $successRate = sprintf('%4.1f', $successRate);
        $successRateColor = $metrics['http_req_failed']['values']['fails'] === $metrics['http_reqs']['values']['count']
            ? 'green'
            : 'red';

        $this->twoColumnDetail('Success Rate', <<<HTML
            <span class="font-bold text-$successRateColor">$successRate %</span>
        HTML);
    }

    /**
     * Prints a two column detail.
     */
    private function twoColumnDetail(string $left, string $right): void
    {
        render(<<<HTML
            <div class="flex max-w-150 mx-2">
                <span>
                    $left
                </span>
                <span class="flex-1 content-repeat-[.] text-gray ml-1"></span>
                <span class="ml-1">
                    $right
                </span>
            </div>
            HTML);
    }

    /**
     * Returns the formatted duration in milliseconds.
     */
    private function ms(float $duration): string
    {
        return sprintf('%4.2f ms', $duration);
    }

    /**
     * Returns the color for the given duration.
     */
    private function color(float $duration, float $excellent, float $ok, float $poor): string
    {
        return match (true) {
            $duration === 0.0 => '',
            $duration <= $excellent => 'text-green font-bold',
            $duration <= $ok => 'text-yellow font-bold',
            $duration <= $poor => 'text-orange font-bold',
            default => 'text-red font-bold',
        };
    }
}
