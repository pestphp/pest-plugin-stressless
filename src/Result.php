<?php

declare(strict_types=1);

namespace Pest\Stressless;

/**
 * @internal
 *
 * @property-read float $averageResponseTime
 * @property-read float $minResponseTime
 * @property-read float $medianResponseTime
 * @property-read float $percentile90ResponseTime
 * @property-read float $percentile95ResponseTime
 * @property-read float $maxResponseTime
 * @property-read int $failedRequests
 * @property-read int $successfulRequests
 * @property-read int $totalRequests
 */
final readonly class Result
{
    /**
     * Creates a new result instance.
     *
     * /**
     *  Creates a new result instance.
     *
     * @param array{
     *      root_group: array{
     *        name: string,
     *        path: string,
     *        id: string
     *      },
     *      options: array{
     *        summaryTimeUnit: string,
     *        noColor: bool,
     *        summaryTrendStats: array{
     *          0: string,
     *          1: string,
     *          2: string,
     *          3: string,
     *          4: string,
     *          5: string
     *        }
     *      },
     *      state: array{
     *        isStdOutTTY: bool,
     *        isStdErrTTY: bool,
     *        testRunDurationMs: float
     *      },
     *      metrics: array{
     *        http_req_duration: array{
     *          type: string,
     *          contains: string,
     *          values: array{
     *            "p(95)": float,
     *            avg: float,
     *            min: float,
     *            med: float,
     *            max: float,
     *            "p(90)": float
     *          }
     *        },
     *        http_req_receiving: array{
     *          type: string,
     *          contains: string,
     *          values: array{
     *            "p(95)": float,
     *            avg: float,
     *            min: float,
     *            med: float,
     *            max: float,
     *            "p(90)": float
     *          }
     *        },
     *        http_req_failed: array{
     *          type: string,
     *          contains: string,
     *          values: array{
     *            rate: int,
     *            passes: int,
     *            fails: int
     *          }
     *        },
     *        http_req_waiting: array{
     *          type: string,
     *          contains: string,
     *          values: array{
     *            "p(95)": float,
     *            avg: float,
     *            min: float,
     *            med: float,
     *            max: float,
     *            "p(90)": float
     *          }
     *        },
     *        iteration_duration: array{
     *          type: string,
     *          contains: string,
     *          values: array{
     *            "p(95)": float,
     *            avg: float,
     *            min: float,
     *            med: float,
     *            max: float,
     *            "p(90)": float
     *          }
     *        },
     *        http_req_blocked: array{
     *          type: string,
     *          contains: string,
     *          values: array{
     *            "p(95)": float,
     *            avg: float,
     *            min: float,
     *            med: float,
     *            max: float,
     *            "p(90)": float
     *          }
     *        },
     *        vus: array{
     *          type: string,
     *          contains: string,
     *          values: array{
     *            value: int,
     *            min: int,
     *            max: int
     *          }
     *        },
     *        http_req_sending: array{
     *          type: string,
     *          contains: string,
     *          values: array{
     *            "p(95)": float,
     *            avg: float,
     *            min: float,
     *            med: float,
     *            max: float,
     *            "p(90)": float
     *          }
     *        },
     *        http_req_connecting: array{
     *          type: string,
     *          contains: string,
     *          values: array{
     *            "p(95)": float,
     *            avg: float,
     *            min: float,
     *            med: float,
     *            max: float,
     *            "p(90)": float
     *          }
     *        },
     *        data_sent: array{
     *          type: string,
     *          contains: string,
     *          values: array{
     *            count: int,
     *            rate: float
     *          }
     *        },
     *        data_received: array{
     *          type: string,
     *          contains: string,
     *          values: array{
     *            "p(95)": float,
     *            avg: float,
     *            min: float,
     *            med: float,
     *            max: float,
     *            "p(90)": float
     *          }
     *        },
     *        vus_max: array{
     *          type: string,
     *          contains: string,
     *          values: array{
     *            value: int,
     *            min: int,
     *            max: int
     *          }
     *        },
     *        http_reqs: array{
     *          type: string,
     *          contains: string,
     *          values: array{
     *            count: int,
     *            rate: float
     *          }
     *        },
     *        iterations: array{
     *          type: string,
     *          contains: string,
     *          values: array{
     *            count: int,
     *            rate: float
     *          }
     *        },
     *        http_req_tls_handshaking: array{
     *          type: string,
     *          contains: string,
     *          values: array{
     *            "p(95)": float,
     *            avg: float,
     *            min: float,
     *            med: float,
     *            max: float,
     *            "p(90)": float
     *          }
     *        },
     *        http_req_duration: array{
     *          type: string,
     *          contains: string,
     *          values: array{
     *            "p(95)": float,
     *            avg: float,
     *            min: float,
     *            med: float,
     *            max: float,
     *            "p(90)": float
     *          }
     *        },
     *      }
     *    } $array
     * /
     */
    public function __construct(
        private array $array,
    ) {
        //
    }

    /**
     * Returns the average response time.
     */
    public function averageResponseTime(): float
    {
        return $this->array['metrics']['http_req_duration']['values']['avg'];
    }

    /**
     * Returns the median response time.
     */
    public function minResponseTime(): float
    {
        return $this->array['metrics']['http_req_duration']['values']['min'];
    }

    /**
     * Returns the median response time.
     */
    public function medianResponseTime(): float
    {
        return $this->array['metrics']['http_req_duration']['values']['med'];
    }

    /**
     * Returns the 90th percentile response time.
     */
    public function percentile90ResponseTime(): float
    {
        return $this->array['metrics']['http_req_duration']['values']['p(90)'];
    }

    /**
     * Returns the 95th percentile response time.
     */
    public function percentile95ResponseTime(): float
    {
        return $this->array['metrics']['http_req_duration']['values']['p(95)'];
    }

    /**
     * Returns the maximum response time.
     */
    public function maxResponseTime(): float
    {
        return $this->array['metrics']['http_req_duration']['values']['max'];
    }

    public function failedRequests(): int
    {
        return $this->array['metrics']['http_req_failed']['values']['passes'];
    }

    public function successfulRequests(): int
    {
        return $this->array['metrics']['http_req_failed']['values']['fails'];
    }

    public function totalRequests(): int
    {
        return $this->array['metrics']['http_reqs']['values']['count'];
    }

    /**
     * Proxies the properties to methods.
     */
    public function __get(string $name): mixed
    {
        return $this->{$name}(); // @phpstan-ignore-line
    }
}
