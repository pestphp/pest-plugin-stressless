<?php

declare(strict_types=1);

namespace Pest\Stressless\Printers;

use function Termwind\render;

/**
 * @internal
 */
final readonly class Info
{
    /**
     * Prints the info.
     */
    public function print(string $info): void
    {
        render(<<<HTML
            <div class="mx-2 max-w-150 text-green font-bold">
                <p>$info</p>
            </div>
        HTML
        );
    }
}
