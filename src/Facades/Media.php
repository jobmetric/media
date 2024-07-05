<?php

namespace JobMetric\Media\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \JobMetric\Media\Services\Common common()
 * @method static \JobMetric\Media\Services\Category category()
 * @method static \JobMetric\Media\Services\File file()
 *
 * @see \JobMetric\Media\Media
 */
class Media extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return \JobMetric\Media\Media::class;
    }
}
