<?php

namespace JobMetric\Media\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void convertToWebp(\JobMetric\Media\Models\Media $media)
 *
 * @see \JobMetric\Media\Services\MediaImage
 */
class MediaImage extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return \JobMetric\Media\Services\MediaImage::class;
    }
}
