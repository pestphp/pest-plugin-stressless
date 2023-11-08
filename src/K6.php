<?php

declare(strict_types=1);

namespace Pest\Stressless;

use PharData;
use RuntimeException;
use Stringable;
use ZipArchive;

final readonly class K6 implements Stringable
{
    /**
     * The path to the bin directory.
     */
    protected const BIN_DIR = __DIR__.'/../bin/';

    /**
     * The version of k6 to download.
     */
    public const K6_VERSION = 'v0.47.0';

    /**
     * The path where the k6 binary is stored relative to the root
     * directory. The arguments are (version, os, arch, extension).
     */
    private const K6 = 'k6-%s-%s-%s/k6%s';

    /**
     * The URL to the k6 binary. The arguments are (version, version, os, arch, extension).
     */
    private const K6_URL = 'https://github.com/grafana/k6/releases/download/%s/k6-%s-%s-%s.%s';

    /**
     * Creates a new binary instance.
     */
    private function __construct(private string $path)
    {
        //
    }

    /**
     * Creates a new binary instance from the environment.
     */
    public static function make(): self
    {
        $path = self::path();

        if (! self::exists()) {
            self::download();
        }

        return new self((string) realpath($path));
    }

    private static function path(): string
    {
        $os = self::os();
        $arch = self::arch();

        return self::BIN_DIR.sprintf(self::K6, self::K6_VERSION, $os, $arch, ($os === 'windows' ? '.exe' : ''));
    }

    public static function exists(): bool
    {
        return file_exists(self::path());
    }

    public static function download(): void
    {
        if (self::exists()) {
            return;
        }

        $os = self::os();
        $arch = self::arch();

        $extension = ($os === 'linux' ? 'tar.gz' : 'zip');
        $url = sprintf(self::K6_URL, self::K6_VERSION, self::K6_VERSION, $os, $arch, $extension);
        $fileName = basename($url);

        if (false === ($binary = file_get_contents($url))) {
            throw new RuntimeException('Unable to download k6 binary.');
        }

        if (file_put_contents(self::BIN_DIR.$fileName, $binary) === false) {
            throw new RuntimeException('Unable to save k6 binary.');
        }

        match ($extension) {
            'tar.gz' => self::extractTarGz($fileName),
            'zip' => self::extractZip($fileName)
        };

        if (! self::ensureExecutable(self::path())) {
            throw new RuntimeException('Unable to make k6 binary executable.');
        }
    }

    /**
     * Extracts the downloaded tar.gz archive
     */
    private static function extractTarGz(string $fileName): void
    {
        $tarGz = new PharData(self::BIN_DIR.$fileName);
        $tarGz->decompress();

        $tar = new PharData(self::BIN_DIR.str_replace('.gz', '', $fileName));
        $tar->extractTo(self::BIN_DIR);

        unlink(self::BIN_DIR.str_replace('.gz', '', $fileName));
        unlink(self::BIN_DIR.$fileName);
    }

    /**
     * Extracts the downloaded zip archive
     */
    private static function extractZip(string $fileName): void
    {
        $zip = new ZipArchive();

        if ($zip->open(self::BIN_DIR.$fileName) !== true) {
            throw new RuntimeException('Unable to open k6 zip archive.');
        }

        $zip->extractTo(self::BIN_DIR);
        $zip->close();

        unlink(self::BIN_DIR.$fileName);
    }

    /**
     * Returns the computer's architecture. Force amd64 on windows
     */
    private static function arch(): string
    {
        if(self::os() === 'windows') {
            return 'amd64';
        }

        return str_contains(php_uname('m'), 'arm') ? 'arm64' : 'amd64';
    }

    /**
     * Returns the operating system.
     */
    private static function os(): string
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
    private static function ensureExecutable(string $binary): bool
    {
        return chmod($binary, 0755);
    }

    /**
     * The string representation of the binary.
     */
    public function __toString(): string
    {
        return $this->path;
    }
}
