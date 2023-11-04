<?php

declare(strict_types=1);

namespace Pest\Stressless\ValueObjects;

use RuntimeException;
use Stringable;

/**
 * @internal
 */
final readonly class Binary implements Stringable
{
    /**
     * The k6 binary name format.
     */
    private const K6 = 'k6-%s-%s';

    /**
     * Creates a new binary instance.
     */
    private function __construct(private string $path)
    {
        //
    }

    /**
     * Creates a new k6 binary instance from the environment.
     */
    public static function k6(): self
    {
        $arch = str_contains(php_uname('m'), 'arm') ? 'arm64' : 'amd64';

        $path = match (PHP_OS_FAMILY) {
            'Darwin' => sprintf(self::K6, 'macos', $arch),
            'Linux' => sprintf(self::K6, 'linux', $arch),
            'Windows' => sprintf(self::K6, 'windows', $arch),
            default => throw new RuntimeException('Unsupported OS.'),
        };

        return new self((string) realpath(__DIR__.'/../../bin/'.$path));
    }

    /**
     * The string representation of the binary.
     */
    public function __toString(): string
    {
        return $this->path;
    }
}
