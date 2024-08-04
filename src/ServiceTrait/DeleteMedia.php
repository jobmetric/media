<?php

namespace JobMetric\Media\ServiceTrait;

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
     * @param int $media_id
     *
     * @return void
     */
    public function delete(int $media_id): void
    {
    }

    /**
     * Restore media
     *
     * @param int $media_id
     *
     * @return void
     */
    public function restore(int $media_id): void
    {
    }

    /**
     * Force Delete media
     *
     * @param int $media_id
     *
     * @return void
     */
    public function forceDelete(int $media_id): void
    {
    }
}
