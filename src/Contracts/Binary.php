<?php

declare(strict_types=1);

namespace Pest\Stressless\Contracts;

use RuntimeException;
use Stringable;

abstract class Binary implements Stringable
{
    /**
     * The path to the bin directory.
     */
    protected const BIN_DIR = __DIR__.'/../../bin/';

    /**
     * Creates a new binary instance from the environment.
     */
    abstract public static function new(): self;

    /**
     * Returns the path to the binary.
     */
    abstract protected static function path(): string;

    /**
     * Checks if the binary exists.
     */
    abstract public static function exists(): bool;

    /**
     * Downloads the binary. Throws an exception if it fails.
     */
    abstract public static function download(): void;

    /**
     * Creates a new binary instance.
     */
    public function __construct(private readonly string $path)
    {
        //
    }

    /**
     * The string representation of the binary.
     */
    public final function __toString(): string
    {
        return $this->path;
    }

    /**
     * Returns the architecture.
     */
    protected final static function arch(): string
    {
        return str_contains(php_uname('m'), 'arm') ? 'arm64' : 'amd64';
    }

    /**
     * Returns the Operating System.
     */
    protected final static function os(): string
    {
        return match (PHP_OS_FAMILY) {
            'Darwin' => 'macos',
            'Linux' => 'linux',
            'Windows' => 'windows',
            default => throw new RuntimeException('Unsupported OS.'),
        };
    }

    /**
     * Make the binary executable.
     */
    protected final static function executable(string $binary): bool
    {
        return chmod($binary, 0755);
    }
}
