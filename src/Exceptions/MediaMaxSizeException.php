<?php

namespace JobMetric\Media\Exceptions;

use Exception;
use Throwable;

class MediaMaxSizeException extends Exception
{
    public function __construct(int $size, int $code = 400, ?Throwable $previous = null)
    {
        parent::__construct(trans('media::base.exceptions.media_max_size', [
            'size' => $size
        ]), $code, $previous);
    }
}
