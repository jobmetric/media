<?php

namespace JobMetric\Media;

/**
 * @property array mediaType
 */
trait MediaableWithType
{
    protected array $mediaType = [];

    /**
     * media allow collections.
     *
     * @return array
     */
    public function mediaAllowCollections(): array
    {
        return $this->getMediaType($this->{$this->getMediaFieldTypeName()});
    }

    /**
     * get media default filed.
     *
     * @return array
     */
    public function getMediaDefaultField(): array
    {
        return [
            'base' => [
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
            ],
        ];
    }

    /**
     * get media filed type name.
     *
     * @return string
     */
    public function getMediaFieldTypeName(): string
    {
        return 'type';
    }

    /**
     * Set media.
     *
     * @param string $type
     * @param array $mediaCollection
     *
     * @return static
     */
    public function setMediaCollection(string $type, array $mediaCollection): static
    {
        $this->mediaType[$type] = array_merge($this->getMediaDefaultField(), $mediaCollection);

        return $this;
    }

    /**
     * Get media by type.
     *
     * @param string $type
     *
     * @return array
     */
    public function getMediaType(string $type): array
    {
        return $this->mediaType[$type] ?? [];
    }
}
