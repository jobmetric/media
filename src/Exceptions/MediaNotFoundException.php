<?php

namespace JobMetric\Media\Exceptions;

use Exception;
use Throwable;

class MediaNotFoundException extends Exception
{
    public function __construct(int|string $media_id_uuid, int $code = 400, ?Throwable $previous = null)
    {
        if (is_int($media_id_uuid)) {
            $trans = trans('media::base.exceptions.media_id_not_found', [
                'media_id' => $media_id_uuid
            ]);
        } else {
            $trans = trans('media::base.exceptions.media_uuid_not_found', [
                'media_uuid' => $media_id_uuid
            ]);
        }

        parent::__construct($trans, $code, $previous);
    }
}
