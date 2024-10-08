<?php

namespace JobMetric\Media\ServiceTrait;

use Illuminate\Support\Facades\Storage;
use JobMetric\Media\Exceptions\MediaIdsAlreadyInTrashException;
use JobMetric\Media\Exceptions\MediaIdsAlreadyNotTrashException;
use JobMetric\Media\Exceptions\MediaIdsNotInParentException;
use JobMetric\Media\Exceptions\MediaIdsUsedInOtherObjectException;
use JobMetric\Media\Facades\Media as MediaFacade;
use JobMetric\Media\Models\Media;
use JobMetric\Media\Models\MediaPath;
use JobMetric\Media\Models\MediaRelation;
use Throwable;

/**
 * Trait DeleteMedia
 *
 * @package JobMetric\Media\ServiceTrait
 */
trait DeleteMedia
{
    /**
     * Delete media
     *
     * @param array $media_ids
     * @param int|null $parent_id
     *
     * @return array
     * @throws Throwable
     */
    public function delete(array $media_ids, int $parent_id = null): array
    {
        $items = Media::withTrashed()->whereIn('id', $media_ids)->get();

        // exception for media ids in parent id
        $parent_flag = false;
        foreach ($items as $item) {
            /**
             * @var Media $item
             */
            if ($item->parent_id != $parent_id) {
                $parent_flag = true;
                break;
            }
        }

        if ($parent_flag) {
            throw new MediaIdsNotInParentException;
        }

        // exception for media ids in trash
        $trash_flag = false;
        foreach ($items as $item) {
            /**
             * @var Media $item
             */
            if ($item->trashed()) {
                $trash_flag = true;
                break;
            }
        }

        if ($trash_flag) {
            throw new MediaIdsAlreadyInTrashException;
        }

        // exception for media ids used in other object
        $ids = MediaPath::query()->whereIn('path_id', $media_ids)->pluck('media_id')->toArray();

        if (MediaRelation::query()->whereIn('media_id', $ids)->exists()) {
            throw new MediaIdsUsedInOtherObjectException;
        }

        // delete media
        Media::destroy($media_ids);

        return [
            'ok' => true,
            'message' => trans('media::base.messages.deleted', ['count' => count($media_ids)]),
            'status' => 200
        ];
    }

    /**
     * Restore media
     *
     * @param array $media_ids
     *
     * @return array
     * @throws Throwable
     */
    public function restore(array $media_ids): array
    {
        $items = Media::withTrashed()->whereIn('id', $media_ids)->get();

        // exception for media ids in normal
        $normal_flag = false;
        foreach ($items as $item) {
            /**
             * @var Media $item
             */
            if (!$item->trashed()) {
                $normal_flag = true;
                break;
            }
        }

        if ($normal_flag) {
            throw new MediaIdsAlreadyNotTrashException;
        }

        // restore media
        $items->each(function ($item) {
            /**
             * @var Media $item
             */
            $item->restore();
        });

        return [
            'ok' => true,
            'message' => trans('media::base.messages.restored', ['count' => count($media_ids)]),
            'status' => 200
        ];
    }

    /**
     * Force Delete media
     *
     * @param array $media_ids
     *
     * @return array
     * @throws Throwable
     */
    public function forceDelete(array $media_ids): array
    {
        $items = Media::withTrashed()->whereIn('id', $media_ids)->get();

        // exception for media ids in normal
        $normal_flag = false;
        foreach ($items as $item) {
            /**
             * @var Media $item
             */
            if (!$item->trashed()) {
                $normal_flag = true;
                break;
            }
        }

        if ($normal_flag) {
            throw new MediaIdsAlreadyNotTrashException;
        }

        // force delete media
        $items->each(function ($item) {
            /**
             * @var Media $item
             */

            // Delete the hard file
            Storage::disk($item->disk)->delete(MediaFacade::getMediaPath($item));

            // Delete the cache file
            $cacheFiles = Storage::disk($item->disk)->files(dirname(MediaFacade::getMediaCachePaths($item)));

            foreach ($cacheFiles as $cacheFile) {
                if (fnmatch(MediaFacade::getMediaCachePaths($item), $cacheFile)) {
                    Storage::disk($item->disk)->delete($cacheFile);
                }
            }

            $item->forceDelete();
        });

        return [
            'ok' => true,
            'message' => trans('media::base.messages.force_deleted', ['count' => count($media_ids)]),
            'status' => 200
        ];
    }
}
