<?php

declare(strict_types=1);

namespace Pest\Stressless;

final readonly class Session
{
    /**
     * The path to session related files.
     */
    private const PATH = '%s/bin/%s_%s.json';

    /**
     * Creates a new session instance.
     */
    public function __construct(
        private string $basePath,
        private string $id,
    ) {
        //
    }

    /**
     * Gets the session's summary file path.
     */
    public function summaryPath(): string
    {
        return sprintf(self::PATH, $this->basePath, $this->id, 'summary');
    }

    /**
     * Gets the session's progress file path.
     */
    public function progressPath(): string
    {
        return sprintf(self::PATH, $this->basePath, $this->id, 'progress');
    }

    /**
     * Cleans the session files, if any.
     */
    public function clean(): void
    {
        if (file_exists($this->progressPath())) {
            unlink($this->progressPath());
        }
        if (file_exists($this->summaryPath())) {
            unlink($this->summaryPath());
        }
    }

    /**
     * Destroys the session instance.
     */
    public function __destruct()
    {
        $this->clean();
    }
}
