<?php

declare(strict_types=1);

namespace Pest\Stressless\ResultPrinters;

use Pest\Stressless\ValueObjects\Result;

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
        $this->server($result);
        $this->network($result);

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

        $this->twoColumnDetail('Total Requests', <<<HTML
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

        $responseDuration = $metrics['http_req_connecting']['values']['avg']
            + $metrics['http_req_tls_handshaking']['values']['avg']
            + $metrics['http_req_duration']['values']['avg'];

        $responseDurationColor = match (true) {
            $responseDuration === 0.0 => '',
            $responseDuration < 200.0 => 'text-green',
            $responseDuration < 400.0 => 'text-yellow',
            $responseDuration < 800.0 => 'text-orange',
            default => 'text-red',
        };

        $responseDuration = sprintf('%4.2f', $responseDuration);

        $this->twoColumnDetail('Response Duration', <<<HTML
            <span class="$responseDurationColor font-bold">$responseDuration ms</span>
        HTML);
    }

    /**
     * Prints the network's detail.
     */
    private function network(Result $result): void
    {
        $metrics = $result->toArray()['metrics'];

        $responseDuration = $metrics['http_req_connecting']['values']['avg']
            + $metrics['http_req_tls_handshaking']['values']['avg']
            + $metrics['http_req_duration']['values']['avg'];

        $responseNetworkDuration = $metrics['http_req_connecting']['values']['avg']
            + $metrics['http_req_tls_handshaking']['values']['avg']
            + $metrics['http_req_duration']['values']['avg']
            - $metrics['http_req_waiting']['values']['avg'];
        $responseNetworkDurationPercentage = $responseDuration > 0.00 ? round($responseNetworkDuration * 100 / $responseDuration, 2) : 0.00;
        $responseNetworkDurationColor = match (true) {
            $responseNetworkDuration === 0.0 => '',
            $responseNetworkDuration < 100.0 => 'text-green',
            $responseNetworkDuration < 200.0 => 'text-yellow',
            $responseNetworkDuration < 400.0 => 'text-orange',
            default => 'text-red',
        };

        $responseNetworkDuration = sprintf('%4.2f', $responseNetworkDuration);

        $this->twoColumnDetail(<<<HTML
            <span class="text-gray mr-1">―</span><span>Network</span>
            <span class="text-gray ml-1">$responseNetworkDurationPercentage %</span>
            HTML, <<<HTML
            <span class="$responseNetworkDurationColor">$responseNetworkDuration ms </span>
        HTML);

        $tlsHandshakingTime = $metrics['http_req_tls_handshaking']['values']['avg'];
        $tlsHandshakingTime = sprintf('%4.2f', $tlsHandshakingTime);

        $dnsLookupTime = $metrics['http_req_connecting']['values']['avg'];
        $dnsLookupTime = sprintf('%4.2f', $dnsLookupTime);

        $uploadTime = $metrics['http_req_sending']['values']['avg'];
        $uploadTime = sprintf('%4.2f', $uploadTime);

        $downloadTime = $metrics['http_req_receiving']['values']['avg'];
        $downloadTime = sprintf('%4.2f', $downloadTime);

        foreach ([
            'TLS Handshaking' => "$tlsHandshakingTime ms",
            'DNS Lookup' => "$dnsLookupTime ms",
            'Upload' => "$uploadTime ms",
            'Download' => "$downloadTime ms",
        ] as $title => $value) {
            $this->twoColumnDetail('<span class="text-gray mr-1 ml-1">∙</span>'.$title, $value);
        }
    }

    /**
     * Prints the server's detail.
     */
    private function server(Result $result): void
    {
        $metrics = $result->toArray()['metrics'];

        $responseDuration = $metrics['http_req_connecting']['values']['avg']
            + $metrics['http_req_tls_handshaking']['values']['avg']
            + $metrics['http_req_duration']['values']['avg'];
        $responseServerDuration = $metrics['http_req_waiting']['values']['avg'];
        $responseServerDurationColor = match (true) {
            $responseServerDuration === 0.0 => '',
            $responseServerDuration < 100.0 => 'text-green',
            $responseServerDuration < 200.0 => 'text-yellow',
            $responseServerDuration < 400.0 => 'text-orange',
            default => 'text-red',
        };

        $responseServerDurationPercentage = $responseDuration > 0.00 ? round($responseServerDuration * 100 / $responseDuration, 2) : 0.00;
        $responseServerDuration = sprintf('%4.2f', $responseServerDuration);

        $this->twoColumnDetail(<<<HTML
            <span class="text-gray mr-1">―</span><span>Server</span>
            <span class="text-gray ml-1">$responseServerDurationPercentage %</span>
            HTML, <<<HTML
            <span class="$responseServerDurationColor">$responseServerDuration ms </span>
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
}
