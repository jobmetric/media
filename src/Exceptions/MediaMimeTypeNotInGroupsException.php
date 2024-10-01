<?php

namespace JobMetric\Media\Exceptions;

use Exception;
use JobMetric\Media\Models\Media;
use Throwable;

class MediaMimeTypeNotInGroupsException extends Exception
{
    public function __construct(Media $media, string|array $groups, int $code = 400, ?Throwable $previous = null)
    {
        parent::__construct(trans('media::base.exceptions.media_mime_type_not_in_groups', [
            'id' => $media->id,
            'mime_type' => $media->mime_type,
            'groups' => implode(', ', (array)$groups)
        ]), $code, $previous);
    }
}
