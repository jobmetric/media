<?php

namespace JobMetric\Media\Exceptions;

use Exception;
use Throwable;

class MediaNameInvalidException extends Exception
{
    public function __construct(string $type, string $name, int $code = 400, ?Throwable $previous = null)
    {
        parent::__construct(trans('media::base.exceptions.media_name_invalid', [
            'type' => $type,
            'name' => $name
        ]), $code, $previous);
    }
}
