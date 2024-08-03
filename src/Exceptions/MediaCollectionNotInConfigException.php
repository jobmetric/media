<?php

namespace JobMetric\Media\Exceptions;

use Exception;
use Throwable;

class MediaCollectionNotInConfigException extends Exception
{
    public function __construct(string $collection, int $code = 400, ?Throwable $previous = null)
    {
        parent::__construct(trans('media::base.exceptions.media_collection_not_in_config', [
            'collection' => $collection
        ]), $code, $previous);
    }
}
