<?php

namespace JobMetric\Media\Exceptions;

use Exception;
use Throwable;

class MediaIdsAlreadyInTrashException extends Exception
{
    public function __construct(int $code = 400, ?Throwable $previous = null)
    {
        parent::__construct(trans('media::base.exceptions.media_ids_already_in_trash'), $code, $previous);
    }
}
