<?php

namespace JobMetric\Media;

use Illuminate\Database\Eloquent\Model;

trait SaveMediaInService
{
    /**
     * Save media for the model.
     *
     * @param Model $model
     * @param array $data
     *
     * @return void
     */
    public function saveMedia(Model $model, array $data = []): void
    {
        $mediaAllowCollections = $model->mediaAllowCollections();

        // detach all media in data
        foreach ($data as $media_collection => $media_value) {
            $model->detachMediaByCollection($media_collection);
        }

        // attach media in data
        foreach ($data as $media_collection => $media_value) {
            if ($mediaAllowCollections[$media_collection]['multiple'] ?? false) {
                foreach ($media_value as $media_item) {
                    $model->attachMedia($media_item, $media_collection);
                }
            } else {
                if ($media_value) {
                    $model->attachMedia($media_value, $media_collection);
                }
            }
        }
    }
}
