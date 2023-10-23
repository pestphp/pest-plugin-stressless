<?php

declare(strict_types=1);

namespace Pest\Stressless\Options;

/**
 * @internal
 */
final readonly class Stage
{
    /**
     * Creates a new stage instance.
     */
    public function __construct(
        private string $duration,
        private int $target,
    ) {
        //
    }

    /**
     * Converts the stage to an array.
     *
     * @return array{duration: string, target: int}
     */
    public function toArray(): array
    {
        return [
            'duration' => $this->duration,
            'target' => $this->target,
        ];
    }
}
