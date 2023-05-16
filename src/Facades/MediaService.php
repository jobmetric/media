<?php

namespace JobMetric\Media\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \JobMetric\Media\Object\Common common()
 * @method static \JobMetric\Media\Object\Category category()
 * @method static \JobMetric\Media\Object\File file()
 *
 * @see \JobMetric\Media\MediaService
 */
class MediaService extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'MediaService';
    }
}
