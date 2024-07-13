<?php

namespace JobMetric\Media\Exceptions;

use Exception;
use Throwable;

class CollectionNotInMediaAllowCollectionMethodException extends Exception
{
    public function __construct(string $collection, int $code = 400, ?Throwable $previous = null)
    {
        parent::__construct(trans('media::base.exceptions.collection_not_in_media_allow_collection_method', [
            'collection' => $collection
        ]), $code, $previous);
    }
}
