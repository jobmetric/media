<?php

namespace JobMetric\Media\Exceptions;

use Exception;
use Throwable;

class MediaRelationNotFoundException extends Exception
{
    public function __construct(string $mediaable_type, int $mediaable_id, int $media_id, int $code = 400, ?Throwable $previous = null)
    {
        parent::__construct(trans('media::base.exceptions.media_relation_not_found', [
            'mediaable_type' => $mediaable_type,
            'mediaable_id' => $mediaable_id,
            'media_id' => $media_id
        ]), $code, $previous);
    }
}
