<?php

namespace JobMetric\Media\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Spatie\QueryBuilder\QueryBuilder query(array $filter = [], array $with = [], string $mode = null)
 * @method static \Illuminate\Http\Resources\Json\AnonymousResourceCollection paginate(array $filter = [], int $page_limit = 15, array $with = [], string $mode = null)
 * @method static \Illuminate\Http\Resources\Json\AnonymousResourceCollection all(array $filter = [], array $with = [], string $mode = null)
 * @method static array newFolder(string $name, int $parent_id = 0)
 * @method static array rename(int $media_id, string $name)
 * @method static bool hasFolder(int $media_id)
 * @method static bool hasFile(int $media_id)
 * @method static array upload(int $parent_id = null, string $collection = 'public', string $field = 'file')
 * @method static \Symfony\Component\HttpFoundation\StreamedResponse download(int $media_id)
 * @method static string temporaryUrl(int $media_id, int $expire_time = 60)
 * @method static void stream(int $media_id)
 * @method static array details(int $media_id, array $with = [])
 * @method static \Illuminate\Http\Resources\Json\AnonymousResourceCollection usedIn(int $media_id)
 * @method static bool hasUsed(int $media_id)
 * @method static array compress(array $media_ids, string $name)
 * @method static void extract(int $media_id)
 * @method static void move(int $media_id, int $parent_id = null)
 * @method static void delete(int $media_id)
 * @method static void restore(int $media_id)
 * @method static void forceDelete(int $media_id)
 * @method static void convertImageToWebp(\JobMetric\Media\Models\Media $media)
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
