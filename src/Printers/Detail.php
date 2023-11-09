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
            <div class="flex mx-2 max-w-150 text-gray">
                <span>Result</span>
                <span class="flex-1 ml-1 content-repeat-[―]"></span>
            </div>
        HTML);

        $this->overview($result);

        $color = $this->color($result->requests->dnsLookup->duration->med, 20.0, 50.0, 100.0);
        $value = $this->ms($result->requests->dnsLookup->duration->med);

        $domain = $result->url();
        $domain = (string) parse_url($domain, PHP_URL_HOST);
        $dnsRecords = dns_get_record($domain, DNS_AAAA + DNS_A);
        $dnsRecords = array_map(fn (array $record): string => $record['ipv6'] ?? $record['ip'], $dnsRecords ?: []);
        $dnsRecords = array_unique($dnsRecords);

        if (count($dnsRecords) > 2) {
            $lastDnsRecord = '(+ '.(count($dnsRecords) - 2).' more)';
            $dnsRecords = array_slice($dnsRecords, 0, 2);
            $dnsRecords[] = $lastDnsRecord;
        }

        $dnsRecords = implode(', ', $dnsRecords);

        $this->twoColumnDetail('DNS Lookup Duration', <<<HTML
            <span class="text-gray mr-1">$dnsRecords</span>
            <span class="$color">$value</span>
        HTML);

        $color = $this->color($result->requests->tlsHandshake->duration->med, 20.0, 50.0, 100.0);
        $value = $this->ms($result->requests->tlsHandshake->duration->med);
        $this->twoColumnDetail('TLS Handshake Duration', <<<HTML
            <span class="$color">$value</span>
        HTML);

        $total = $result->requests->duration->med;
        $color = $this->color($total, 100.0, 300.0, 1000.0);

        $this->twoColumnDetail(
            'Request Duration',
            '<span class="'.$color.'">'.$this->ms($total).'</span>'
        );

        $color = $this->color($result->requests->upload->duration->med, 50.0, 150.0, 250.0);
        $value = $result->requests->upload->duration->med;
        $percentage = $total === 0.0 ? 0.0 : ($value * 100.0 / $total);
        $percentage = sprintf('%4.1f', $percentage);
        $value = $this->ms($value);

        $dataRate = $result->requests->upload->data->rate() / 1024.0 / 1024.0;
        $dataRate = sprintf('%4.2f', $dataRate);

        $dataPerRequestRate = $result->requests->upload->data->rate() / 1024.0 / 1024.0 / $result->requests->count;
        $dataPerRequestRate = sprintf('%4.2f', $dataPerRequestRate);

        $this->twoColumnDetail(<<<HTML
            <span>— Upload</span>
            <span class="ml-1 text-gray">$percentage %</span>
        HTML, <<<HTML
            <span class="text-gray mr-1">$dataPerRequestRate MB/req</span>
            <span class="text-gray mr-1">$dataRate MB/s</span>
            <span class="$color">$value</span>
        HTML);

        $color = $this->color($result->requests->ttfb->duration->med, 50.0, 150.0, 400.0);
        $value = $result->requests->ttfb->duration->med;
        $percentage = $total === 0.0 ? 0.0 : ($value * 100.0 / $total);
        $percentage = sprintf('%4.1f', $percentage);
        $value = $this->ms($value);

        $this->twoColumnDetail(<<<HTML
            <span>— TTFB</span>
            <span class="ml-1 text-gray">$percentage % — including server processing time</span>
        HTML, "<span class=\"$color\">$value</span>");

        $color = $this->color($result->requests->download->duration->med, 100.0, 300.0, 1000.0);
        $value = $result->requests->download->duration->med;
        $percentage = $total === 0.0 ? 0.0 : ($value * 100.0 / $total);
        $percentage = sprintf('%4.1f', $percentage);
        $value = $this->ms($value);

        $dataRate = $result->requests->download->data->rate() / 1024.0 / 1024.0;
        $dataRate = sprintf('%4.2f', $dataRate);

        $dataPerRequestRate = $result->requests->download->data->rate() / 1024.0 / 1024.0 / $result->requests->count;
        $dataPerRequestRate = sprintf('%4.2f', $dataPerRequestRate);

        $this->twoColumnDetail(<<<HTML
            <span>— Download</span>
            <span class="ml-1 text-gray">$percentage %</span>
        HTML, <<<HTML
            <span class="text-gray mr-1">$dataPerRequestRate MB/req</span>
            <span class="text-gray mr-1">$dataRate MB/s</span>
            <span class="$color">$value</span>
        HTML);

        render(<<<'HTML'
            <div class="mx-2 mb-1 max-w-150 text-right text-gray">
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
            </div>
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

        $testRunConcurrency = $result->testRun()->concurrency();

        $this->twoColumnDetail('Test Duration', "$testRunDuration s");
        $this->twoColumnDetail('Test Concurrency', "$testRunConcurrency");

        $requestsTotal = $metrics['http_reqs']['values']['count'];
        $requestsRate = round($metrics['http_reqs']['values']['rate'], 2);
        $result->testRun()->concurrency();

        $this->twoColumnDetail('Requests Count', <<<HTML
            <span class="text-gray mr-1">$requestsRate reqs/s</span>
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
