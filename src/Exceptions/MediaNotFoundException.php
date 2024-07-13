<?php

namespace JobMetric\Media\Exceptions;

use Exception;
use Throwable;

class MediaNotFoundException extends Exception
{
    public function __construct(int $media_id, int $code = 400, ?Throwable $previous = null)
    {
        parent::__construct(trans('media::base.exceptions.media_not_found', [
            'media_id' => $media_id
        ]), $code, $previous);
    }
}
