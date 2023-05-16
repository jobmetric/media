<?php

namespace JobMetric\Media\Exceptions;

use Exception;
use Throwable;

class FolderNotFoundException extends Exception
{
    public function __construct(int $code = 400, ?Throwable $previous = null)
    {
        parent::__construct('Folder not found!', $code, $previous);
    }
}
