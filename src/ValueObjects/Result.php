<?php

declare(strict_types=1);

namespace Pest\Stressless\ValueObjects;

/**
 * @internal
 *
 * @property-read float $successRate
 * @property-read float $failureRate
 * @property-read int $requests
 * @property-read int $successfulRequests
 * @property-read int $failedRequests
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
     * Gets the rate of successful requests, as a percentage, between "0.00" and "100.00".
     */
    public function successRate(): float
    {
        $successfulRequests = $this->array['metrics']['http_req_failed']['values']['fails'];
        $totalRequests = $this->array['metrics']['http_reqs']['values']['count'];

        $percentage = (float) ($successfulRequests * 100 / $totalRequests);

        return min(max(0.00, $percentage), 100.00);
    }

    /**
     * Gets the rate of failed requests, as a percentage, between "0.00" and "100.00".
     */
    public function failureRate(): float
    {
        return 100.00 - $this->successRate();
    }

    /**
     * Gets the total number of requests.
     */
    public function requests(): int
    {
        return $this->array['metrics']['http_reqs']['values']['count'];
    }

    /**
     * Gets the total number of successful requests.
     */
    public function successfulRequests(): int
    {
        return $this->array['metrics']['http_req_failed']['values']['fails'];
    }

    /**
     * Gets the total number of failed requests.
     */
    public function failedRequests(): int
    {
        return $this->array['metrics']['http_req_failed']['values']['passes'];
    }

    /**
     * Proxies the properties to methods.
     */
    public function __get(string $name): mixed
    {
        return $this->{$name}(); // @phpstan-ignore-line
    }

    /**
     * Returns the raw array.
     *
     * @return array{
     *       root_group: array{
     *         name: string,
     *         path: string,
     *         id: string
     *       },
     *       options: array{
     *         summaryTimeUnit: string,
     *         noColor: bool,
     *         summaryTrendStats: array{
     *           0: string,
     *           1: string,
     *           2: string,
     *           3: string,
     *           4: string,
     *           5: string
     *         }
     *       },
     *       state: array{
     *         isStdOutTTY: bool,
     *         isStdErrTTY: bool,
     *         testRunDurationMs: float
     *       },
     *       metrics: array{
     *         http_req_duration: array{
     *           type: string,
     *           contains: string,
     *           values: array{
     *             "p(95)": float,
     *             avg: float,
     *             min: float,
     *             med: float,
     *             max: float,
     *             "p(90)": float
     *           }
     *         },
     *         http_req_receiving: array{
     *           type: string,
     *           contains: string,
     *           values: array{
     *             "p(95)": float,
     *             avg: float,
     *             min: float,
     *             med: float,
     *             max: float,
     *             "p(90)": float
     *           }
     *         },
     *         http_req_failed: array{
     *           type: string,
     *           contains: string,
     *           values: array{
     *             rate: int,
     *             passes: int,
     *             fails: int
     *           }
     *         },
     *         http_req_waiting: array{
     *           type: string,
     *           contains: string,
     *           values: array{
     *             "p(95)": float,
     *             avg: float,
     *             min: float,
     *             med: float,
     *             max: float,
     *             "p(90)": float
     *           }
     *         },
     *         iteration_duration: array{
     *           type: string,
     *           contains: string,
     *           values: array{
     *             "p(95)": float,
     *             avg: float,
     *             min: float,
     *             med: float,
     *             max: float,
     *             "p(90)": float
     *           }
     *         },
     *         http_req_blocked: array{
     *           type: string,
     *           contains: string,
     *           values: array{
     *             "p(95)": float,
     *             avg: float,
     *             min: float,
     *             med: float,
     *             max: float,
     *             "p(90)": float
     *           }
     *         },
     *         vus: array{
     *           type: string,
     *           contains: string,
     *           values: array{
     *             value: int,
     *             min: int,
     *             max: int
     *           }
     *         },
     *         http_req_sending: array{
     *           type: string,
     *           contains: string,
     *           values: array{
     *             "p(95)": float,
     *             avg: float,
     *             min: float,
     *             med: float,
     *             max: float,
     *             "p(90)": float
     *           }
     *         },
     *         http_req_connecting: array{
     *           type: string,
     *           contains: string,
     *           values: array{
     *             "p(95)": float,
     *             avg: float,
     *             min: float,
     *             med: float,
     *             max: float,
     *             "p(90)": float
     *           }
     *         },
     *         data_sent: array{
     *           type: string,
     *           contains: string,
     *           values: array{
     *             count: int,
     *             rate: float
     *           }
     *         },
     *         data_received: array{
     *           type: string,
     *           contains: string,
     *           values: array{
     *             "p(95)": float,
     *             avg: float,
     *             min: float,
     *             med: float,
     *             max: float,
     *             "p(90)": float
     *           }
     *         },
     *         vus_max: array{
     *           type: string,
     *           contains: string,
     *           values: array{
     *             value: int,
     *             min: int,
     *             max: int
     *           }
     *         },
     *         http_reqs: array{
     *           type: string,
     *           contains: string,
     *           values: array{
     *             count: int,
     *             rate: float
     *           }
     *         },
     *         iterations: array{
     *           type: string,
     *           contains: string,
     *           values: array{
     *             count: int,
     *             rate: float
     *           }
     *         },
     *         http_req_tls_handshaking: array{
     *           type: string,
     *           contains: string,
     *           values: array{
     *             "p(95)": float,
     *             avg: float,
     *             min: float,
     *             med: float,
     *             max: float,
     *             "p(90)": float
     *           }
     *         },
     *         http_req_duration: array{
     *           type: string,
     *           contains: string,
     *           values: array{
     *             "p(95)": float,
     *             avg: float,
     *             min: float,
     *             med: float,
     *             max: float,
     *             "p(90)": float
     *           }
     *         },
     *       }
     *     }
     *  /
     * /
     */
    public function toArray(): array
    {
        return $this->array;
    }
}
