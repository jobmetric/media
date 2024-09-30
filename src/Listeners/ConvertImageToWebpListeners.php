<?php

namespace JobMetric\Media\Listeners;

use JobMetric\Media\Events\UploadFileEvent;
use JobMetric\Media\Jobs\ConvertImageToWebpJobs;

class ConvertImageToWebpListeners
{
    /**
     * Handle the event.
     */
    public function handle(UploadFileEvent $event): void
    {
        if (in_array($event->media->mime_type, config('media.mime_type.image')) && config('media.webp_convert.enable')) {
            ConvertImageToWebpJobs::dispatch($event->media);
        }
    }
}
