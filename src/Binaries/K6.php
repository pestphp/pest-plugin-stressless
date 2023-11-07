<?php

declare(strict_types=1);

namespace Pest\Stressless\Binaries;

use PharData;
use RuntimeException;
use ZipArchive;
use Pest\Stressless\Contracts\Binary;

final class K6 extends Binary
{
    /**
     * The k6 version to use.
     */
    public const K6_VERSION = 'v0.47.0';

    /**
     * The k6 binary name format.
     * The order of the arguments is (version, os, arch, extension).
     */
    private const K6 = 'k6-%s-%s-%s/k6%s';

    /**
     * The k6 binary url format.
     * The order of the arguments is (version, version, os, arch, extension).
     */
    private const K6_URL = 'https://github.com/grafana/k6/releases/download/%s/k6-%s-%s-%s.%s';

    /**
     * @inheritDoc
     */
    public static function new(): self
    {
        /**
         * Get the path to the binary
         */
        $path = self::path();

        /**
         * If the file does not exist, download it
         */
        if (!self::exists()) {
            self::download();
        }

        return new self((string) realpath($path));
    }

    /**
     * @inheritDoc
     */
    protected static function path(): string
    {
        $os   = self::os();
        $arch = self::arch();

        return self::BIN_DIR.sprintf(self::K6, self::K6_VERSION, $os, $arch, ($os === 'windows' ? '.exe' : ''));
    }

    /**
     * @inheritDoc
     */
    public static function exists(): bool
    {
        return file_exists(self::path());
    }

    /**
     * @inheritDoc
     */
    public static function download(): void
    {
        if(self::exists()) {
            return;
        }

        $os   = self::os();
        $arch = self::arch();

        $extension = ($os === 'linux' ? 'tar.gz' : 'zip');
        $url       = sprintf(self::K6_URL, self::K6_VERSION, self::K6_VERSION, $os, $arch, $extension);
        $fileName  = basename($url);

        /**
         * Download the archive
         */
        if (false === ($binary = file_get_contents($url))) {
            throw new RuntimeException('Unable to download k6 binary.');
        }
        if (false === file_put_contents(self::BIN_DIR.$fileName, $binary)) {
            throw new RuntimeException('Unable to save k6 binary.');
        }

        match ($extension) {
            'tar.gz' => self::extractTarGz($fileName),
            'zip' => self::extractZip($fileName)
        };

        if (!self::executable(self::path())) {
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

        /**
         * Remove the archive
         */
        unlink(self::BIN_DIR.str_replace('.gz', '', $fileName));
        unlink(self::BIN_DIR.$fileName);
    }

    /**
     * Extracts the downloaded zip archive
     */
    private static function extractZip(string $fileName): void
    {
        $zip = new ZipArchive();

        if (true !== $zip->open(self::BIN_DIR.$fileName)) {
            throw new RuntimeException('Unable to open k6 zip archive.');
        }

        $zip->extractTo(self::BIN_DIR);
        $zip->close();

        /**
         * Remove the archive
         */
        unlink(self::BIN_DIR.$fileName);
    }
}
