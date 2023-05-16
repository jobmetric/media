<?php

namespace JobMetric\Media\Exceptions;

use Exception;
use Throwable;

class DiskNotDefinedException extends Exception
{
    public function __construct(string $disk, int $code = 400, ?Throwable $previous = null)
    {
        parent::__construct('Disk "'.$disk.'" is not defined!', $code, $previous);
    }
}
