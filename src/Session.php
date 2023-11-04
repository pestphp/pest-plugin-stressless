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
        private int $concurrentRequests,
        private int $duration,
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
     * Gets the number of concurrent requests.
     */
    public function concurrentRequests(): int
    {
        return $this->concurrentRequests;
    }

    /**
     * Gets the duration of the run.
     */
    public function duration(): int
    {
        return $this->duration;
    }

    /**
     * Destroys the session instance.
     */
    public function __destruct()
    {
        $this->clean();
    }
}
