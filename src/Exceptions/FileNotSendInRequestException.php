<?php

namespace JobMetric\Media\Exceptions;

use Exception;
use Throwable;

class FileNotSendInRequestException extends Exception
{
    public function __construct(string $filed, int $code = 400, ?Throwable $previous = null)
    {
        parent::__construct('Field '.$filed.' is not available in the sent request!', $code, $previous);
    }
}
