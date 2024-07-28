<?php

namespace JobMetric\Media\Events;

use JobMetric\Media\Models\Media;

class NewFolderEvent
{
    public Media $media;

    /**
     * Create a new event instance.
     *
     * @param Media $media
     *
     * @return void
     */
    public function __construct(Media $media)
    {
        $this->media = $media;
    }
}
