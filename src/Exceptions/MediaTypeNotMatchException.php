<?php

namespace JobMetric\Media\Exceptions;

use Exception;
use Throwable;

class MediaTypeNotMatchException extends Exception
{
    public function __construct(int $media_id, string $type, int $code = 400, ?Throwable $previous = null)
    {
        parent::__construct(trans('media::base.exceptions.media_type_not_match', [
            'media_id' => $media_id,
            'type' => $type
        ]), $code, $previous);
    }
}
