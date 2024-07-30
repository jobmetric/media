<?php

namespace JobMetric\Media\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Spatie\QueryBuilder\QueryBuilder query(array $filter = [], array $with = [])
 * @method static \Illuminate\Http\Resources\Json\AnonymousResourceCollection paginate(array $filter = [], int $page_limit = 15, array $with = [])
 * @method static \Illuminate\Http\Resources\Json\AnonymousResourceCollection all(array $filter = [], array $with = [])
 * @method static array newFolder(string $name, int $parent_id = 0)
 * @method static array rename(int $media_id, string $name)
 * @method static bool hasFolder(int $media_id)
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
