<?php

namespace JobMetric\Media\Exceptions;

use Exception;
use Throwable;

class MediaMimeTypeException extends Exception
{
    public function __construct(string $mime_type, int $code = 400, ?Throwable $previous = null)
    {
        parent::__construct(trans('media::base.exceptions.media_mime_type', [
            'mime_type' => $mime_type
        ]), $code, $previous);
    }
}
