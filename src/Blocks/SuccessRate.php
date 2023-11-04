<?php

declare(strict_types=1);

namespace Pest\Stressless\Blocks;

use Pest\Stressless\Contracts\Block;
use Pest\Stressless\ValueObjects\Result;

/**
 * @internal
 */
final readonly class SuccessRate implements Block
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

        $percentage = (float) ($array['metrics']['http_req_failed']['values']['fails'] * 100 / $array['metrics']['http_reqs']['values']['count']);

        return sprintf('%4.1f %%', $percentage);
    }

    /**
     * Gets the block color.
     */
    public function color(): string
    {
        $array = $this->result->toArray();

        return $array['metrics']['http_req_failed']['values']['fails'] === $array['metrics']['http_reqs']['values']['count']
            ? 'green'
            : 'red';
    }
}
