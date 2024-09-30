<?php

namespace JobMetric\Media;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class MediaEventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        \JobMetric\Media\Events\UploadFileEvent::class => [
            \JobMetric\Media\Listeners\ConvertImageToWebpListeners::class,
        ],
    ];
}
