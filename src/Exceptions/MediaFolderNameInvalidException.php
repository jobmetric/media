<?php

namespace JobMetric\Media\Exceptions;

use Exception;
use Throwable;

class MediaFolderNameInvalidException extends Exception
{
    public function __construct(string $name, int $code = 400, ?Throwable $previous = null)
    {
        parent::__construct(trans('media::base.exceptions.media_folder_name_invalid', [
            'name' => $name
        ]), $code, $previous);
    }
}
