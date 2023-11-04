<?php

declare(strict_types=1);

namespace Pest\Stressless\Blocks;

use Pest\Stressless\Contracts\Block;
use Pest\Stressless\ValueObjects\Result;

/**
 * @internal
 */
final readonly class ServerDuration implements Block
{
    /**
     * Creates a block instance.
     */
    public function __construct(
        private Result $result,
    ) {
        //
    }

    /**
     * Gets the block label.
     */
    public function value(): string
    {
        $array = $this->result->toArray();

        $duration = $array['metrics']['http_req_waiting']['values']['avg'];

        $duration = sprintf('%4.2f ms', $duration);

        if (strlen($duration) < 9) {
            $duration = str_pad($duration, 9, ' ', STR_PAD_BOTH);
        }

        return $duration;
    }

    /**
     * Gets the block color.
     */
    public function color(): string
    {
        $array = $this->result->toArray();

        $duration = $array['metrics']['http_req_waiting']['values']['avg'];

        return match (true) {
            $duration < 100 => 'green',
            $duration < 200 => 'yellow',
            default => 'red',
        };
    }
}
