<?php

namespace JobMetric\Media;

use JobMetric\Media\ServiceType\Media;

/**
 * @property static array $dynamicMedia
 */
trait HasDynamicFile
{
    protected static array $dynamicMedia = [];

    /**
     * Boot Has Dynamic File
     *
     * @return void
     */
    public static function bootHasDynamicFile(): void
    {
        $serviceType = getServiceTypeClass(static::class);

        $types = $serviceType->getTypes();

        foreach ($types as $type) {
            $innerType = $serviceType->type($type);

            if ($innerType->hasBaseMedia()) {
                self::$dynamicMedia[$type]['base'] = [
                    'media_collection' => 'public',
                    'size' => [
                        'default' => [
                            'w' => config('media.default_image_size.width'),
                            'h' => config('media.default_image_size.width'),
                        ],
                        'thumb' => [
                            'w' => config('media.thumb_image_size.width'),
                            'h' => config('media.thumb_image_size.width'),
                        ]
                    ]
                ];
            }

            foreach ($innerType->getMedia() as $media) {
                /**
                 * @var Media $media
                 */
                self::$dynamicMedia[$type][$media->getCollection()] = [
                    'media_collection' => $media->getMediaCollection(),
                    'multiple' => $media->getMultiple(),
                    'size' => $media->getSize(),
                ];
            }
        }
    }

    /**
     * media allow collections.
     *
     * @return array
     */
    public function mediaAllowCollections(): array
    {
        return self::$dynamicMedia[$this->{$this->dynamicMediaFieldTypeName()}] ?? [];
    }

    /**
     * media filed type name.
     *
     * @return string
     */
    public function dynamicMediaFieldTypeName(): string
    {
        return 'type';
    }
}
