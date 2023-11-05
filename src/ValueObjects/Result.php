<?php

declare(strict_types=1);

namespace Pest\Stressless\ValueObjects;

use Pest\Stressless\Result\Requests;
use Pest\Stressless\Result\TestRun;

/**
 * @internal
 *
 * @property-read Requests $requests
 * @property-read TestRun $testRun
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
     *             type: string,
     *             contains: string,
     *             values: array{
     *               count: int,
     *               rate: float
     *             }
     *           },
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
     * Returns the details of the requests.
     */
    public function requests(): Requests
    {
        return new Requests($this);
    }

    /**
     * Returns the details of the test run.
     */
    public function testRun(): TestRun
    {
        return new TestRun($this);
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
     *            type: string,
     *            contains: string,
     *            values: array{
     *              count: int,
     *              rate: float
     *            }
     *          },
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
