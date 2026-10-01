<?php

namespace JobMetric\Media\Exceptions;

use RuntimeException;

/** A recoverable file operation requiring a user's conflict choice. */
class FileManagerConflict extends RuntimeException
{
    public function __construct(public string $reason, public array $details = [])
    {
        parent::__construct($reason, 409);
    }
}
