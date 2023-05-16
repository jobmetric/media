<?php

namespace JobMetric\Media\Exceptions;

use Exception;
use Throwable;

class DuplicateFileException extends Exception
{
    public function __construct(int $code = 400, ?Throwable $previous = null)
    {
        parent::__construct('This file has already been posted in this collection!', $code, $previous);
    }
}
