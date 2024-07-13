<?php

namespace JobMetric\Media\Exceptions;

use Exception;
use Throwable;

class MediaCollectionNotMatchException extends Exception
{
    public function __construct(int $media_id, string $media_collection, string $collection, int $code = 400, ?Throwable $previous = null)
    {
        parent::__construct(trans('media::base.exceptions.media_collection_not_match', [
            'media_id' => $media_id,
            'media_collection' => $media_collection,
            'collection' => $collection
        ]), $code, $previous);
    }
}
