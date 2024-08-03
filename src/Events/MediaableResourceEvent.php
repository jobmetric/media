<?php

namespace JobMetric\Media\Events;

class MediaableResourceEvent
{
    /**
     * The mediaable model instance.
     *
     * @var mixed
     */
    public mixed $mediaable;

    /**
     * The resource to be filled by the listener.
     *
     * @var mixed|null
     */
    public mixed $resource;

    /**
     * Create a new event instance.
     *
     * @param mixed $mediaable
     */
    public function __construct(mixed $mediaable)
    {
        $this->mediaable = $mediaable;
        $this->resource = null;
    }
}
