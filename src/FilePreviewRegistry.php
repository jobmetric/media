<?php

namespace JobMetric\Media;

use Closure;
use InvalidArgumentException;

/** Register trusted server-side preview readers without changing the core dispatcher. */
class FilePreviewRegistry
{
    /** @var array<string, Closure> */
    private array $readers = [];

    /** Register a reader receiving a validated local path, media record and options. */
    public function register(string $extension, Closure $reader): void
    {
        $extension = strtolower(ltrim($extension, '.'));
        if (!preg_match('/^[a-z0-9]{1,16}$/', $extension)) throw new InvalidArgumentException('Invalid preview extension.');
        $this->readers[$extension] = $reader;
    }

    /** Resolve a registered reader, if any. */
    public function get(string $extension): ?Closure
    {
        return $this->readers[strtolower($extension)] ?? null;
    }
}
