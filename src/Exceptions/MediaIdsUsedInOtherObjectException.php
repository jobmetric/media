<?php

namespace JobMetric\Media\Exceptions;

use Exception;
use Throwable;

class MediaIdsUsedInOtherObjectException extends Exception
{
    public function __construct(int $code = 400, ?Throwable $previous = null)
    {
        parent::__construct(trans('media::base.exceptions.media_ids_used_in_other_object'), $code, $previous);
    }
}
