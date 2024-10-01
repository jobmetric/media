<?php

namespace JobMetric\Media\Facades;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Facade;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * @method static void convertToWebp(\JobMetric\Media\Models\Media $media)
 * @method static JsonResponse|BinaryFileResponse|StreamedResponse responsive(string $media_uuid, int $width = null, int $height = null)
 *
 * @see \JobMetric\Media\Services\MediaImage
 */
class MediaImage extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return \JobMetric\Media\Services\MediaImage::class;
    }
}
