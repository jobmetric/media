<?php

namespace JobMetric\Media\ServiceTrait;

use JobMetric\Media\Exceptions\MediaIdsAlreadyInTrashException;
use JobMetric\Media\Exceptions\MediaIdsNotInParentException;
use JobMetric\Media\Exceptions\MediaIdsUsedInOtherObjectException;
use JobMetric\Media\Http\Resources\MediaResource;
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

        /**
         * @var Media $item
         */

        // exception for media ids in parent id
        $parent_flag = false;
        foreach ($items as $item) {
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
     * @param int|string $parent_id
     *
     * @return array
     */
    public function restore(array $media_ids, int|string $parent_id = ''): array
    {
    }

    /**
     * Force Delete media
     *
     * @param array $media_ids
     * @param int|string $parent_id
     *
     * @return array
     */
    public function forceDelete(array $media_ids, int|string $parent_id = ''): array
    {
    }
}
