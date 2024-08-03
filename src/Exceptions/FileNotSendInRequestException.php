<?php

namespace JobMetric\Media\Exceptions;

use Exception;
use Throwable;

class FileNotSendInRequestException extends Exception
{
    public function __construct(string $filed, int $code = 400, ?Throwable $previous = null)
    {
        parent::__construct(trans('media::base.exceptions.file_not_send_in_request', [
            'field' => $filed
        ]), $code, $previous);
    }
}
