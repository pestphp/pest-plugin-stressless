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

        /**
         * data_received..................: 22 kB 5.7 kB/s
         * data_sent......................: 742 B 198 B/s
         * http_req_blocked...............: avg=1.05s    min=1.05s    med=1.05s    max=1.05s    p(90)=1.05s    p(95)=1.05s
         * http_req_connecting............: avg=334.26ms min=334.26ms med=334.26ms max=334.26ms p(90)=334.26ms p(95)=334.26ms
         * http_req_duration..............: avg=2.7s     min=2.7s     med=2.7s     max=2.7s     p(90)=2.7s     p(95)=2.7s
         * { expected_response:true }...: avg=2.7s     min=2.7s     med=2.7s     max=2.7s     p(90)=2.7s     p(95)=2.7s
         * http_req_failed................: 0.00% ✓ 0        ✗ 1
         * http_req_receiving.............: avg=112.41µs min=112.41µs med=112.41µs max=112.41µs p(90)=112.41µs p(95)=112.41µs
         * http_req_sending...............: avg=294.48µs min=294.48µs med=294.48µs max=294.48µs p(90)=294.48µs p(95)=294.48µs
         * http_req_tls_handshaking.......: avg=700.6ms  min=700.6ms  med=700.6ms  max=700.6ms  p(90)=700.6ms  p(95)=700.6ms
         * http_req_waiting...............: avg=2.7s     min=2.7s     med=2.7s     max=2.7s     p(90)=2.7s     p(95)=2.7s
         * http_reqs......................: 1     0.266167/s
         * iteration_duration.............: avg=3.75s    min=3.75s    med=3.75s    max=3.75s    p(90)=3.75s    p(95)=3.75s
         * iterations.....................: 1     0.266167/s
         * vus............................: 1     min=1      max=1
         * vus_max........................: 1     min=1      max=1
         */
        $metrics = $result->toArray()['metrics'];

        $this->overview($result, $metrics);
        $this->server($metrics);
        $this->network($metrics);

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
    private function overview(Result $result, array $metrics): void
    {
        $testRunDuration = $result->testRunDuration();
        $testRunDuration = sprintf('%4.2f', $testRunDuration / 1000);

        $this->twoColumnDetail('Test Duration', "$testRunDuration s");

        $requestsTotal = $metrics['http_reqs']['values']['count'];
        $requestsRate = round($metrics['http_reqs']['values']['rate'], 2);
        $result->testRunConcurrentUsers();

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
    private function network(array $metrics): void
    {
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
    private function server(array $metrics): void
    {
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
