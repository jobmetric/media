<?php

namespace JobMetric\Media\Exceptions;

use Exception;
use Throwable;

class MediaMustInSameFolderException extends Exception
{
    public function __construct(int $code = 400, ?Throwable $previous = null)
    {
        parent::__construct(trans('media::base.exceptions.media_must_in_same_folder'), $code, $previous);
    }
}
